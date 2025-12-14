<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReportPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string $view = 'filament.pages.sales-report-page';
    
    protected static ?string $navigationLabel = 'Laporan Penjualan';
    
    protected static ?string $title = 'Laporan Penjualan';
    
    protected static ?string $navigationGroup = 'Manajemen';
    
    protected static ?int $navigationSort = 2;

    public ?array $data = [];
    public $salesData = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->startOfMonth(),
            'date_to' => now(),
            'status' => 'completed',
        ]);
        
        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date_from')
                    ->label('Dari Tanggal')
                    ->required()
                    ->default(now()->startOfMonth()),
                    
                DatePicker::make('date_to')
                    ->label('Sampai Tanggal')
                    ->required()
                    ->default(now()),
                    
                Select::make('status')
                    ->label('Status Order')
                    ->options([
                        'all' => 'Semua Status',
                        'completed' => 'Completed',
                        'paid' => 'Paid',
                        'processing' => 'Processing',
                        'serving' => 'Serving',
                    ])
                    ->default('completed'),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function generateReport(): void
    {
        $dateFrom = Carbon::parse($this->data['date_from']);
        $dateTo = Carbon::parse($this->data['date_to']);
        $status = $this->data['status'];

        $query = Order::whereBetween('created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()]);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->with(['items.menu', 'payment', 'table'])->get();

        $this->salesData = [
            'total_orders' => $orders->count(),
            'total_sales' => $orders->sum('total_price'),
            'average_order' => $orders->count() > 0 ? $orders->avg('total_price') : 0,
            'orders_by_status' => $orders->groupBy('status')->map->count(),
            'orders_by_date' => $orders->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_price'),
                ];
            }),
            'top_selling_menus' => $orders->flatMap->items
                ->groupBy('menu_id')
                ->map(function ($items) {
                    return [
                        'menu_name' => $items->first()->menu->name ?? 'Unknown',
                        'quantity' => $items->sum('quantity'),
                        'total_sales' => $items->sum(function ($item) {
                            return $item->subtotal;
                        }),
                    ];
                })
                ->sortByDesc('quantity')
                ->take(10),
            'orders_by_payment_method' => $orders->filter(function ($order) {
                return $order->payment !== null;
            })->groupBy('payment.payment_method')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_price'),
                ];
            }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('generate')
                ->label('Generate Laporan')
                ->icon('heroicon-o-arrow-path')
                ->action('generateReport'),
                
            \Filament\Actions\Action::make('export')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('exportPdf'),
        ];
    }

    public function exportPdf()
    {
        $pdf = Pdf::loadView('pdf.sales-report', [
            'salesData' => $this->salesData,
            'dateFrom' => $this->data['date_from'],
            'dateTo' => $this->data['date_to'],
            'status' => $this->data['status'],
        ]);
        
        $filename = 'laporan-penjualan-' . now()->format('Y-m-d-His') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}