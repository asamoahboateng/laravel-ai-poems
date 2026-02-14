<div
    class="-m-6 flex h-[calc(100vh-4rem)]"
    x-data="{
        sidebarOpen: true,
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messageContainer;
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    }"
    x-init="scrollToBottom()"
>
    {{-- Left sidebar --}}
    <aside
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        class="flex w-72 shrink-0 flex-col border-r border-gray-200 bg-gray-50"
    >
        {{-- New Chat button --}}
        <div class="p-3">
            <button
                wire:click="newConversation"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Chat
            </button>
        </div>

        {{-- Provider / Model selectors --}}
        <div class="space-y-2 border-b border-gray-200 px-3 pb-3">
            <div>
                <label for="provider" class="block text-xs font-medium text-gray-500">Provider</label>
                <select
                    id="provider"
                    wire:model.live="selectedProvider"
                    class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    @foreach (\App\Livewire\Chat::providerLabels() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="model" class="block text-xs font-medium text-gray-500">Model</label>
                <select
                    id="model"
                    wire:model="selectedModel"
                    class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    @foreach ($this->currentModels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Conversation list --}}
        <div class="flex-1 overflow-y-auto p-3">
            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Conversations</h3>
            @forelse ($this->conversations as $convo)
                <div
                    wire:key="convo-{{ $convo->id }}"
                    class="group mb-1 flex items-center justify-between rounded-lg px-3 py-2 text-sm cursor-pointer transition {{ $conversationId === $convo->id ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200' : 'text-gray-700 hover:bg-gray-100' }}"
                    wire:click="selectConversation({{ $convo->id }})"
                >
                    <span class="truncate">{{ $convo->title }}</span>
                    <button
                        wire:click.stop="deleteConversation({{ $convo->id }})"
                        class="ml-2 hidden shrink-0 text-gray-400 hover:text-red-500 group-hover:block"
                        title="Delete"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            @empty
                <p class="py-4 text-center text-xs text-gray-400">No conversations yet.</p>
            @endforelse
        </div>
    </aside>

    {{-- Main chat area --}}
    <div class="flex flex-1 flex-col bg-gray-50">
        {{-- Toggle sidebar + header --}}
        <div class="flex h-12 shrink-0 items-center gap-3 border-b border-gray-200 bg-white px-4">
            <button @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-200">
                    {{ \App\Livewire\Chat::providerLabels()[$selectedProvider] ?? $selectedProvider }}
                </span>
                <span class="text-gray-300">/</span>
                <span class="font-medium text-gray-700">
                    {{ $this->currentModels[$selectedModel] ?? $selectedModel }}
                </span>
            </div>
        </div>

        {{-- Messages --}}
        <div
            class="flex-1 overflow-y-auto px-4 py-6"
            x-ref="messageContainer"
        >
            @if (empty($messages) && !$isStreaming)
                <div class="flex h-full items-center justify-center">
                    <div class="text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50">
                            <svg class="h-8 w-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-gray-900">Start a conversation</h3>
                        <p class="mt-1 text-sm text-gray-500">Choose a provider and model, then type your message below.</p>
                    </div>
                </div>
            @else
                <div class="mx-auto max-w-3xl space-y-6">
                    @foreach ($messages as $msg)
                        @if ($msg['role'] === 'user')
                            <div class="flex items-start justify-end gap-3">
                                <div class="max-w-[80%] rounded-2xl rounded-tr-sm bg-indigo-600 px-4 py-3 text-sm leading-relaxed text-white shadow-sm">
                                    {!! nl2br(e($msg['content'])) !!}
                                </div>
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-700 text-xs font-bold text-white">
                                    AI
                                </div>
                                <div class="prose prose-sm max-w-[80%] rounded-2xl rounded-tl-sm bg-white px-4 py-3 shadow-sm ring-1 ring-gray-200">
                                    {!! str($msg['content'])->markdown() !!}
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Streaming response --}}
                    @if ($isStreaming)
                        <div class="flex items-start gap-3" x-init="scrollToBottom()">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-700 text-xs font-bold text-white">
                                AI
                            </div>
                            <div class="prose prose-sm max-w-[80%] rounded-2xl rounded-tl-sm bg-white px-4 py-3 shadow-sm ring-1 ring-gray-200">
                                <span wire:stream="answer">{!! str($streamedAnswer)->markdown() !!}</span>
                                <span class="ml-1 inline-block h-4 w-1 animate-pulse rounded-full bg-indigo-400"></span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Input area --}}
        <div class="shrink-0 border-t border-gray-200 bg-white p-4">
            <form
                wire:submit="sendMessage"
                class="mx-auto flex max-w-3xl items-end gap-3"
                x-data
            >
                <div class="relative flex-1">
                    <textarea
                        wire:model="messageInput"
                        placeholder="Type your message..."
                        rows="1"
                        class="block w-full resize-none rounded-xl border-gray-300 px-4 py-3 pr-12 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50"
                        @disabled($isStreaming)
                        @keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage(); }"
                        x-init="$el.focus()"
                        x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 200) + 'px'"
                    ></textarea>
                </div>
                <button
                    type="submit"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
                    @disabled($isStreaming)
                    title="Send message"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                    </svg>
                </button>
            </form>
            <p class="mx-auto mt-2 max-w-3xl text-center text-xs text-gray-400">
                Press Enter to send, Shift+Enter for new line
            </p>
        </div>
    </div>
</div>
