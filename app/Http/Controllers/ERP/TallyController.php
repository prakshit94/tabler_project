<?php
namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\TallySyncLog;
use App\Models\SystemSetting;
use App\Services\TallyService;
use App\Jobs\SyncToTallyJob;
use Illuminate\Http\Request;

class TallyController extends Controller
{
    public function __construct(private TallyService $tallyService) {}

    /** Tally sync dashboard */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $query  = TallySyncLog::with('createdBy')->latest();
        if ($status) $query->where('status', $status);

        $logs = $query->paginate(20)->withQueryString();

        $summary = [
            'pending' => TallySyncLog::where('status', 'pending')->count(),
            'success' => TallySyncLog::where('status', 'success')->count(),
            'failed'  => TallySyncLog::where('status', 'failed')->count(),
        ];

        $settings = SystemSetting::where('group', 'tally')->get()->keyBy('key');

        return view('erp.tally.index', compact('logs', 'summary', 'status', 'settings'));
    }

    public function show(TallySyncLog $log)
    {
        return view('erp.tally.show', compact('log'));
    }

    /** Manual sync a single log */
    public function sync(TallySyncLog $log)
    {
        $success = $this->tallyService->sync($log);
        $msg = $success ? "Synced successfully." : "Sync failed. Check the log for details.";
        return redirect()->route('erp.tally.index')->with($success ? 'success' : 'error', $msg);
    }

    /** Sync all pending */
    public function syncAll()
    {
        $synced = $this->tallyService->syncAllPending();
        return redirect()->route('erp.tally.index')->with('success', "Synced {$synced} pending log(s).");
    }

    /** Retry all failed */
    public function retryFailed()
    {
        $queued = $this->tallyService->retryFailed();
        return redirect()->route('erp.tally.index')->with('success', "Queued {$queued} failed log(s) for retry.");
    }

    /** Update settings */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'tally_sync_mode'  => 'required|in:manual,scheduled,instant',
            'tally_url'        => 'required|url',
            'tally_company'    => 'required|string|max:200',
            'tally_max_retries'=> 'required|integer|min:1|max:10',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return redirect()->route('erp.tally.index')->with('success', 'Tally settings updated.');
    }

    /** Preview XML payload */
    public function previewXml(TallySyncLog $log)
    {
        return response($log->payload, 200)->header('Content-Type', 'text/xml');
    }
}
