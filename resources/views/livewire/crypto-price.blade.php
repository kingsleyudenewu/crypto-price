<div>
    <h2>Crypto Prices ({{ count($cryptoPairs) }} pairs)</h2>
    <table class="min-w-full bg-white">
        <thead>
        <tr>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                Currency Pair
            </th>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                Average Price
            </th>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                Price Change
            </th>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                Last Updated
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach ($cryptoPairs as $pair)
            <tr wire:key="{{ $pair->id }}" id="crypto-pair-{{ $pair->id }}">
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                    {{ $pair->pair }}
                </td>
                <td class="price px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                    ${{ number_format($pair->average_price, 2) }}
                </td>
                <td class="price-change px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                    <span class="{{ $pair->price_change >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $pair->price_change >= 0 ? '↑' : '↓' }}
                        ${{ number_format(abs($pair->price_change), 2) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                    {{ $pair->last_updated->diffForHumans() }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
    // document.addEventListener('DOMContentLoaded', function () {
    //     window.Echo.channel('crypto-prices')
    //         .listen('.CryptoPriceUpdated', (event) => {
    //             Livewire.emit('refreshCryptoPairs');
    //         });
    // });
</script>
