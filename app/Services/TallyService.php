<?php
namespace App\Services;

use App\Models\TallySyncLog;
use App\Models\SystemSetting;
use App\Jobs\SyncToTallyJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TallyService
{
    // Voucher type mapping
    const VOUCHER_MAP = [
        'sales_invoice'    => 'Sales Voucher',
        'payment_received' => 'Receipt Voucher',
        'purchase'         => 'Purchase Voucher',
        'sales_return'     => 'Credit Note',
        'purchase_return'  => 'Debit Note',
    ];

    /**
     * Queue a transaction for Tally sync based on the configured mode.
     */
    public function queueSync(string $referenceType, int $referenceId, string $txnType, array $data = []): TallySyncLog
    {
        $voucherType = self::VOUCHER_MAP[$txnType] ?? $txnType;
        $xml = $this->buildXml($txnType, $data);

        $log = TallySyncLog::create([
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'voucher_type'   => $voucherType,
            'payload'        => $xml,
            'status'         => 'pending',
            'retry_count'    => 0,
            'created_by'     => auth()->id(),
        ]);

        $mode = SystemSetting::get('tally_sync_mode', 'manual');

        if ($mode === 'instant') {
            SyncToTallyJob::dispatch($log)->onQueue('tally');
        }
        // 'manual' and 'scheduled' — just leave as pending, triggered later

        return $log;
    }

    /**
     * Perform actual sync to Tally (called from Job or manual trigger).
     */
    public function sync(TallySyncLog $log): bool
    {
        $log->update(['last_attempt_at' => now()]);

        try {
            $url = SystemSetting::get('tally_url', 'http://localhost:9000');

            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'text/xml'])
                ->post($url, $log->payload);

            if ($response->successful()) {
                $log->update([
                    'status'    => 'success',
                    'response'  => $response->body(),
                    'synced_at' => now(),
                ]);
                return true;
            }

            $log->update([
                'status'        => 'failed',
                'response'      => $response->body(),
                'retry_count'   => $log->retry_count + 1,
                'error_message' => "HTTP {$response->status()}",
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error("Tally sync failed for log #{$log->id}: " . $e->getMessage());
            $log->update([
                'status'        => 'failed',
                'retry_count'   => $log->retry_count + 1,
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Retry all failed syncs under max retry limit.
     */
    public function retryFailed(): int
    {
        $maxRetries = (int) SystemSetting::get('tally_max_retries', 3);
        $logs = TallySyncLog::retryable($maxRetries)->get();
        $count = 0;

        foreach ($logs as $log) {
            SyncToTallyJob::dispatch($log)->onQueue('tally');
            $count++;
        }

        return $count;
    }

    /**
     * Build XML payload for Tally voucher.
     */
    public function buildXml(string $type, array $data): string
    {
        $company = SystemSetting::get('tally_company', 'My Company');
        $voucherType = self::VOUCHER_MAP[$type] ?? $type;
        $date = ($data['date'] ?? now()->format('Ymd'));
        $narration = htmlspecialchars($data['narration'] ?? '');
        $amount = number_format((float)($data['amount'] ?? 0), 2, '.', '');

        $entriesXml = '';
        foreach ($data['entries'] ?? [] as $entry) {
            $ledgerName = htmlspecialchars($entry['ledger'] ?? '');
            $entryAmount = number_format((float)($entry['amount'] ?? 0), 2, '.', '');
            $entriesXml .= "<ALLLEDGERENTRIES.LIST>
                <LEDGERNAME>{$ledgerName}</LEDGERNAME>
                <ISDEEMEDPOSITIVE>" . ($entry['is_debit'] ? 'Yes' : 'No') . "</ISDEEMEDPOSITIVE>
                <AMOUNT>{$entryAmount}</AMOUNT>
            </ALLLEDGERENTRIES.LIST>";
        }

        return <<<XML
<ENVELOPE>
  <HEADER>
    <TALLYREQUEST>Import Data</TALLYREQUEST>
  </HEADER>
  <BODY>
    <IMPORTDATA>
      <REQUESTDESC>
        <REPORTNAME>Vouchers</REPORTNAME>
        <STATICVARIABLES>
          <SVCURRENTCOMPANY>{$company}</SVCURRENTCOMPANY>
        </STATICVARIABLES>
      </REQUESTDESC>
      <REQUESTDATA>
        <TALLYMESSAGE xmlns:UDF="TallyUDF">
          <VOUCHER VCHTYPE="{$voucherType}" ACTION="Create">
            <DATE>{$date}</DATE>
            <VOUCHERTYPENAME>{$voucherType}</VOUCHERTYPENAME>
            <NARRATION>{$narration}</NARRATION>
            <AMOUNT>{$amount}</AMOUNT>
            {$entriesXml}
          </VOUCHER>
        </TALLYMESSAGE>
      </REQUESTDATA>
    </IMPORTDATA>
  </BODY>
</ENVELOPE>
XML;
    }

    /**
     * Sync all pending logs (used by scheduled command).
     */
    public function syncAllPending(): int
    {
        $logs = TallySyncLog::pending()->get();
        $synced = 0;
        foreach ($logs as $log) {
            if ($this->sync($log)) {
                $synced++;
            }
        }
        return $synced;
    }
}
