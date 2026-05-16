<x-layouts.dashboard title="Site users">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">Site users</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Breeze / Sanctum accounts in the <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-800">users</code> table (read-only).
            </p>
        </div>

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-950">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Name</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Email</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Domain</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Registered</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-zinc-500">Last login</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($users as $user)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">{{ $user->email }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ str_contains($user->email, '@') ? \Illuminate\Support\Str::after($user->email, '@') : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $user->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                    @if ($user->latestLogin)
                                        {{ $user->latestLogin->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-zinc-500">No users yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
