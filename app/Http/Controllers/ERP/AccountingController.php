<?php
namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\AccountingTransaction;
use App\Models\AccountingEntry;
use App\Models\Ledger;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $type  = $request->input('type');
        $from  = $request->input('from');
        $to    = $request->input('to');
        $query = AccountingTransaction::with(['entries.ledger', 'createdBy'])->latest('transaction_date');

        if ($type) $query->where('type', $type);
        if ($from) $query->whereDate('transaction_date', '>=', $from);
        if ($to)   $query->whereDate('transaction_date', '<=', $to);

        $transactions = $query->paginate(20)->withQueryString();
        $types = AccountingTransaction::distinct()->pluck('type');

        return view('erp.accounting.index', compact('transactions', 'types', 'type', 'from', 'to'));
    }

    public function show(AccountingTransaction $transaction)
    {
        $transaction->load(['entries.ledger', 'createdBy']);
        return view('erp.accounting.show', compact('transaction'));
    }

    /** Ledger balances / trial balance */
    public function trialBalance()
    {
        $ledgers = Ledger::with(['entries'])->get()->map(function ($ledger) {
            $totalDebit  = AccountingEntry::where('ledger_id', $ledger->id)->sum('debit');
            $totalCredit = AccountingEntry::where('ledger_id', $ledger->id)->sum('credit');
            $ledger->total_debit  = $totalDebit;
            $ledger->total_credit = $totalCredit;
            $ledger->balance      = $totalDebit - $totalCredit;
            return $ledger;
        });

        $grandDebit  = $ledgers->sum('total_debit');
        $grandCredit = $ledgers->sum('total_credit');

        return view('erp.accounting.trial-balance', compact('ledgers', 'grandDebit', 'grandCredit'));
    }

    /** Ledger statement for a specific ledger */
    public function ledgerStatement(Request $request, Ledger $ledger)
    {
        $from    = $request->input('from', now()->startOfMonth()->toDateString());
        $to      = $request->input('to', now()->toDateString());
        $entries = AccountingEntry::with('transaction')
            ->where('ledger_id', $ledger->id)
            ->whereBetween('entry_date', [$from, $to])
            ->orderBy('entry_date')
            ->get();

        $totalDebit  = $entries->sum('debit');
        $totalCredit = $entries->sum('credit');

        return view('erp.accounting.ledger-statement', compact('ledger', 'entries', 'totalDebit', 'totalCredit', 'from', 'to'));
    }

    public function ledgers()
    {
        $ledgers = Ledger::withCount('entries')->latest()->paginate(20);
        return view('erp.accounting.ledgers', compact('ledgers'));
    }
}
