<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Offert {{ $quote->quote_number }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $quote->title }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('sales-enablement.quotes.pdf', $quote) }}"
                   class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Ladda ner PDF
                </a>
                <a href="{{ route('sales-enablement.quotes') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Tillbaka
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Quote Preview Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Header Section -->
                <div class="p-8 border-b-4 border-amber-500">
                    <div class="flex justify-between items-start">
                        <div>
                            @php
                                $companyLogo = \App\Models\SystemSetting::get('app_logo');
                                $companyName = \App\Models\SystemSetting::get('app_name', config('app.name', 'Stafe CRM'));
                                $companyAddress = \App\Models\SystemSetting::get('company_address', '');
                                $companyPhone = \App\Models\SystemSetting::get('company_phone', '');
                                $companyEmail = \App\Models\SystemSetting::get('company_email', '');
                            @endphp
                            @if($companyLogo)
                                <img src="{{ $companyLogo }}" alt="{{ $companyName }}" class="h-12 mb-4">
                            @endif
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $companyName }}</h3>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                @if($companyAddress){{ $companyAddress }}<br>@endif
                                @if($companyPhone)Tel: {{ $companyPhone }}<br>@endif
                                @if($companyEmail){{ $companyEmail }}@endif
                            </div>
                        </div>
                        <div class="text-right">
                            <h1 class="text-3xl font-bold text-amber-600 uppercase tracking-wider">Offert</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $quote->quote_number }}</p>
                            <span class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($quote->status === 'accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($quote->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($quote->status === 'sent') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($quote->status === 'expired') bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                @endif">
                                {{ ucfirst($quote->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Customer & Quote Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-8 bg-gray-50 dark:bg-gray-900">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Till</h4>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $quote->company->name }}</p>
                        @if($quote->contact)
                            <p class="text-gray-600 dark:text-gray-400">{{ $quote->contact->first_name }} {{ $quote->contact->last_name }}</p>
                            @if($quote->contact->email)
                                <p class="text-gray-500 dark:text-gray-500">{{ $quote->contact->email }}</p>
                            @endif
                        @endif
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Offertdetaljer</h4>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $quote->title }}</p>
                        <p class="text-gray-600 dark:text-gray-400">Skapad av: {{ $quote->createdBy->name ?? 'N/A' }}</p>
                        @if($quote->deal)
                            <p class="text-gray-500 dark:text-gray-500">Affar: {{ $quote->deal->name }}</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Info Bar -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-amber-50 dark:bg-amber-900/20 border-y border-amber-200 dark:border-amber-800">
                    <div class="text-center">
                        <p class="text-xs font-bold text-amber-800 dark:text-amber-200 uppercase">Offertdatum</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $quote->created_at->format('Y-m-d') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold text-amber-800 dark:text-amber-200 uppercase">Giltig till</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $quote->valid_until ? $quote->valid_until->format('Y-m-d') : 'Ej specificerat' }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold text-amber-800 dark:text-amber-200 uppercase">Valuta</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $quote->currency ?? 'SEK' }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold text-amber-800 dark:text-amber-200 uppercase">Referens</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $quote->quote_number }}</p>
                    </div>
                </div>

                <!-- Description -->
                @if($quote->description)
                <div class="p-8">
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Beskrivning</h4>
                    <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $quote->description }}</p>
                </div>
                @endif

                <!-- Items Table -->
                <div class="p-8">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-amber-500">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider rounded-tl-lg">Produkt/Tjanst</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">Antal</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-white uppercase tracking-wider">A-pris</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-white uppercase tracking-wider">Rabatt</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-white uppercase tracking-wider rounded-tr-lg">Summa</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($quote->items as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $item->name }}</div>
                                    @if($item->description)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $item->description }}</div>
                                    @endif
                                    @if($item->sku)
                                        <div class="text-xs text-gray-400 dark:text-gray-500">Art.nr: {{ $item->sku }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-gray-900 dark:text-white">{{ number_format($item->quantity, 0) }}</td>
                                <td class="px-6 py-4 text-right text-gray-900 dark:text-white">{{ number_format($item->unit_price, 2, ',', ' ') }}</td>
                                <td class="px-6 py-4 text-right text-gray-500 dark:text-gray-400">
                                    @if($item->discount_percent > 0)
                                        {{ number_format($item->discount_percent, 0) }}%
                                    @elseif($item->discount_amount > 0)
                                        {{ number_format($item->discount_amount, 2, ',', ' ') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">{{ number_format($item->line_total, 2, ',', ' ') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    Inga produkter/tjanster har lagts till i denna offert.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="p-8 flex justify-end">
                    <div class="w-full max-w-sm">
                        <div class="space-y-2">
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Delsumma</span>
                                <span>{{ number_format($quote->subtotal ?? 0, 2, ',', ' ') }} {{ $quote->currency ?? 'SEK' }}</span>
                            </div>
                            @if($quote->discount_amount > 0)
                            <div class="flex justify-between text-red-600 dark:text-red-400">
                                <span>Rabatt</span>
                                <span>-{{ number_format($quote->discount_amount, 2, ',', ' ') }} {{ $quote->currency ?? 'SEK' }}</span>
                            </div>
                            @endif
                            @if($quote->tax_amount > 0)
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Moms (25%)</span>
                                <span>{{ number_format($quote->tax_amount, 2, ',', ' ') }} {{ $quote->currency ?? 'SEK' }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-xl font-bold text-white bg-amber-500 px-4 py-3 rounded-lg -mx-4">
                                <span>Totalt</span>
                                <span>{{ number_format($quote->total_amount ?? 0, 2, ',', ' ') }} {{ $quote->currency ?? 'SEK' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                @if($quote->terms_and_conditions && count($quote->terms_and_conditions) > 0)
                <div class="p-8 bg-amber-50 dark:bg-amber-900/20 border-t border-amber-200 dark:border-amber-800">
                    <h4 class="text-sm font-bold text-amber-800 dark:text-amber-200 mb-3">Villkor</h4>
                    <ul class="space-y-1 text-sm text-amber-900 dark:text-amber-100">
                        @foreach($quote->terms_and_conditions as $term)
                            <li class="flex items-start">
                                <span class="text-amber-500 mr-2">-</span>
                                {{ $term }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Signatures -->
                <div class="p-8 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">For leverantor</h4>
                            <div class="border-t border-gray-300 dark:border-gray-600 pt-4">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $quote->createdBy->name ?? '' }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $companyName }}</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500">Datum: {{ $quote->created_at->format('Y-m-d') }}</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">For kund (Godkannande)</h4>
                            @if($quote->signatures->isNotEmpty())
                                @php $signature = $quote->signatures->first(); @endphp
                                @if($signature->signature_data)
                                    <img src="data:image/png;base64,{{ $signature->signature_data }}" alt="Signatur" class="h-16 mb-2">
                                @endif
                                <div class="border-t border-gray-300 dark:border-gray-600 pt-4">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $signature->signer_name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $signature->signer_title ?? '' }}</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500">Signerad: {{ $signature->signed_at->format('Y-m-d H:i') }}</p>
                                </div>
                            @else
                                <div class="h-16 border-b-2 border-dashed border-gray-300 dark:border-gray-600 mb-2"></div>
                                <div class="pt-2">
                                    <p class="text-sm text-gray-400 dark:text-gray-500">Ej signerad</p>
                                    @if(in_array($quote->status, ['sent', 'viewed']))
                                        <a href="{{ route('sales-enablement.quotes.sign', $quote) }}"
                                           class="inline-flex items-center mt-3 px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                            Signera offert
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
