<template>
    <!-- background -->
    <div class="relative min-h-screen bg-cover bg-center" style="background-image: url('/background.png')">
        <!-- overlay (ใส่ตรงนี้) -->
        <div class="absolute inset-0 bg-blue-900/40"></div>

        <!-- content -->
        <div class="relative z-10 flex items-center justify-center min-h-screen">
            <div class="w-full max-w-md">
                <!-- Card -->
                <div class="bg-white/90 backdrop-blur shadow-2xl rounded-2xl p-8">
                    <!-- Logo -->
                    <div class="text-center mb-8">
                        <img src="/logo.png" alt="Smart Office" class="mx-auto h-16 mb-4" />
                        <h1 class="text-2xl font-bold tracking-tight text-gray-800">
                            Smart Office
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            ระบบสารบรรณอิเล็กทรอนิกส์ สตส.ตาก
                        </p>
                    </div>

                    <!-- content จาก Login.vue -->
                    <slot />
                </div>

                <!-- Footer -->
                <p class="text-sm text-gray-500 mt-1">
                    ระบบสารบรรณอิเล็กทรอนิกส์ สตส.ตาก
                </p>
            </div>
        </div>
    </div>
</template>
