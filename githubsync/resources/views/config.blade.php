<div>
    <form wire:submit.prevent="save">
        <div>
            <label for="token">GitHub Token</label>
            <input type="text" id="token" wire:model="token">
        </div>

        <div>
            <label for="username">GitHub Username</label>
            <input type="text" id="username" wire:model="username">
        </div>

        <div>
            <label for="password">GitHub Password</label>
            <input type="password" id="password" wire:model="password">
        </div>

        <button type="submit">Save</button>
    </form>

    @if (session()->has('message'))
        <div>
            {{ session('message') }}
        </div>
    @endif
</div>