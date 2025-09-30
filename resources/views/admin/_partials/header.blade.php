<header
    class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-6">
    <h1 class="text-lg font-semibold">კეთილი იყოს თქვენი მობრძანება!</h1>

    <div class="flex items-center space-x-4">
        <!-- Theme Switcher -->
        <button
            @click="toggleTheme()"
            class="p-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
            title="Toggle theme"
        >
            <template x-if="!isDark">
                <x-icon-sun class="h-6 w-6 text-yellow-500" role="img" title="ნათელი ფონი" />
            </template>
            <template x-if="isDark">
                <x-icon-moon class="h-6 w-6 text-gray-300" role="img" title="ბნელი ფონი" />
            </template>
        </button>

        <!-- User Profile Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @keydown.escape="open = false" type="button"
                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-full"
                    id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                <img class="h-10 w-10 rounded-full object-cover" src="https://i.pravatar.cc/40" alt="User avatar" />
            </button>

            <!-- Dropdown menu -->
            <div x-show="open"
                 @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-20"
                 role="menu"
                 aria-orientation="vertical"
                 aria-labelledby="user-menu-button"
                 tabindex="-1"
            >
                <form method="POST" action="/logout">
                    <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                            role="menuitem" tabindex="-1">
                        გასვლა
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
