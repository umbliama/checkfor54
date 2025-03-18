<div class="group-item" data-id="{{ $group->id }}">

<a class="group-link" href="/chatify/group/{{ $group->id }}">
<div class="avatar av-m" style="background-image: url('{{ asset('storage/' . $group->avatar) }}');"></div>
    <div class="group-info">
        <p>{{ $group->name }}</p>
    </div>
</a>
</div>

<style>
    .group-link {
        display: flex;
        gap: 20px;
    }
</style>