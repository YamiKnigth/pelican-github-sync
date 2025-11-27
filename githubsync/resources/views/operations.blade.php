<div>
    <form wire:submit.prevent="cloneRepository">
        <div>
            <label for="repositoryUrl">Repository URL</label>
            <input type="text" id="repositoryUrl" wire:model="repositoryUrl">
        </div>

        <div>
            <label for="directory">Directory</label>
            <input type="text" id="directory" wire:model="directory">
        </div>

        <button type="submit">Clone</button>
    </form>

    <button wire:click="pullRepository">Pull</button>
    <button wire:click="pushRepository">Push</button>

    @if (session()->has('message'))
        <div>
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div>
            {{ session('error') }}
        </div>
    @endif
</div>