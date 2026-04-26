<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $sale->invoice_no }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-info {
            flex: 1;
        }
        .invoice-info {
            text-align: right;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .invoice-no {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .bill-to, .ship-to {
            flex: 1;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .totals-label {
            font-weight: bold;
        }
        .totals-value {
            text-align: right;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .notes {
            margin-top: 30px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #ccc;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                @if($company = \App\Models\Company::first())
                    <div class="company-name">{{ $company->name }}</div>
                    <div>{{ $company->address }}</div>
                    <div>{{ $company->city }}, {{ $company->state }} {{ $company->postal_code }}</div>
                    <div>Phone: {{ $company->phone }}</div>
                    <div>Email: {{ $company->email }}</div>
                    <div>Tax ID: {{ $company->tax_id }}</div>
                @else
                    <div class="company-name">Inventory System</div>
                    <div>123 Business Street, City, State 12345</div>
                    <div>Phone: (123) 456-7890 | Email: info@example.com</div>
                @endif
            </div>
            <div class="invoice-info">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-no">#{{ $sale->invoice_no }}</div>
                <div>Date: {{ $sale->date->format('F d, Y') }}</div>
                <div>Due Date: {{ $sale->date->addDays(30)->format('F d, Y') }}</div>
                <div>
                    Status: 
                    <span class="status-badge status-{{ $sale->payment_status }}">
                        {{ strtoupper($sale->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="details">
            <div class="bill-to">
                <div class="section-title">Bill To</div>
                @if($sale->customer)
                    <div><strong>{{ $sale->customer->name }}</strong></div>
                    <div>{{ $sale->customer->address }}</div>
                    <div>{{ $sale->customer->city }}, {{ $sale->customer->state }} {{ $sale->customer->postal_code }}</div>
                    <div>Phone: {{ $sale->customer->phone }}</div>
                    <div>Email: {{ $sale->customer->email }}</div>
                @else
                    <div><strong>Walk-in Customer</strong></div>
                @endif
            </div>
            <div class="ship-to">
                <div class="section-title">Ship To / Store</div>
                @if($sale->store)
                    <div><strong>{{ $sale->store->name }}</strong></div>
                    <div>{{ $sale->store->address }}</div>
                    <div>{{ $sale->store->city }}, {{ $sale->store->state }} {{ $sale->store->postal_code }}</div>
                    <div>Phone: {{ $sale->store->phone }}</div>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Tax</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->discount, 2) }}</td>
                    <td class="text-right">{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <div class="totals-label">Subtotal:</div>
                <div class="totals-value">{{ number_format($sale->total_amount, 2) }}</div>
            </div>
            @if($sale->discount > 0)
            <div class="totals-row">
                <div class="totals-label">Discount:</div>
                <div class="totals-value">-{{ number_format($sale->discount, 2) }}</div>
            </div>
            @endif
            @if($sale->shipping > 0)
            <div class="totals-row">
                <div class="totals-label">Shipping:</div>
                <div class="totals-value">{{ number_format($sale->shipping, 2) }}</div>
            </div>
            @endif
            @if($sale->tax_amount > 0)
            <div class="totals-row">
                <div class="totals-label">Tax:</div>
                <div class="totals-value">{{ number_format($sale->tax_amount, 2) }}</div>
            </div>
            @endif
            <div class="totals-row grand-total">
                <div class="totals-label">TOTAL:</div>
                <div class="totals-value">{{ number_format($sale->net_amount, 2) }}</div>
            </div>
            <div class="totals-row">
                <div class="totals-label">Paid Amount:</div>
                <div class="totals-value">{{ number_format($sale->paid_amount, 2) }}</div>
            </div>
            <div class="totals-row">
                <div class="totals-label">Balance Due:</div>
                <div class="totals-value">{{ number_format($sale->net_amount - $sale->paid_amount, 2) }}</div>
            </div>
        </div>

        @if($sale->payments->count() > 0)
        <div style="margin-top: 30px;">
            <div class="section-title">Payment History</div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Payment Method</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->payments as $payment)
                    <tr>
                        <td>{{ $payment->date->format('M d, Y') }}</td>
                        <td>{{ $payment->payment_method }}</td>
                        <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($sale->notes)
        <div class="notes">
            <strong>Notes:</strong> {{ $sale->notes }}
        </div>
        @endif

        <div class="footer">
            <div>Thank you for your business!</div>
            <div>If you have any questions about this invoice, please contact us.</div>
            <div style="margin-top: 10px;">
                <strong>Terms & Conditions:</strong> Payment is due within 30 days. Late payments may be subject to a 1.5% monthly finance charge.
            </div>
        </div>
    </div>
</body>
</html>