@props([
    'data' => [],
])

<div class="grid grid-cols-1 lg:grid-cols-6 gap-6 items-center">

    {{-- Chart --}}
    <div class="lg:col-span-5 relative w-full h-[300px]">
        <canvas
            data-akpd-chart
            data-data='@json($data)'
        ></canvas>
    </div>

    {{-- Keterangan --}}
    <div class="lg:col-span-1">

        <p class="text-sm font-semibold text-[#086375] mb-5">
            Keterangan
        </p>

        <div class="space-y-5">

            {{-- Ya --}}
            <div class="flex items-start gap-3">
                <span class="w-3 h-3 rounded-sm bg-[#FF6B6B] mt-1 shrink-0"></span>

                <div>
                    <p class="text-sm font-medium text-gray-700">
                        Ya
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        Terindikasi Masalah
                    </p>
                </div>
            </div>

            {{-- Tidak --}}
            <div class="flex items-start gap-3">
                <span class="w-3 h-3 rounded-sm bg-[#086375] mt-1 shrink-0"></span>

                <div>
                    <p class="text-sm font-medium text-gray-700">
                        Tidak
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        Kondisi Baik
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>