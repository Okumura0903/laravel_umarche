<x-tests.app>
    <x-slot name="header">
        ヘッダー２
    </x-slot>
    コンポーネントテスト２
    <x-test-class-base classBaseMessage="メッセージです"></x-test-class-base>
    <div class="mb-4">
        <x-test-class-base classBaseMessage="メッセージです" defaultMessage="初期値から変更しています"></x-test-class-base>
    </div>
</x-tests.app>