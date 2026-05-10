<div class="search-wrap">
    <form action="{{ route('rooms.index') }}" method="GET" class="search-card">
        
        <!-- Row 1 -->
        <div class="search-field" style="grid-column: span 1;">
            <label>Hotel / Room Name</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search name...">
        </div>
        
        <div class="search-field">
            <label>Check-in</label>
            <input type="date" name="check_in" value="{{ request('check_in') }}" min="{{ date('Y-m-d') }}">
        </div>
        
        <div class="search-field">
            <label>Check-out</label>
            <input type="date" name="check_out" value="{{ request('check_out') }}" min="{{ date('Y-m-d') }}">
        </div>

        <div class="search-field">
            <label>Room Type</label>
            <select name="room_type_id">
                <option value="">All Types</option>
                @foreach(\App\Helpers\RoomHelper::getRoomTypes(tenant() ? tenant()->id : null) as $type)
                    <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->room_type }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Row 2 -->
        <div class="search-field">
            <label>Adults</label>
            <select name="adults">
                @for($i=1; $i<=6; $i++)
                    <option value="{{ $i }}" {{ request('adults') == $i ? 'selected' : '' }}>{{ $i }} Adult(s)</option>
                @endfor
            </select>
        </div>

        <div class="search-field">
            <label>Children</label>
            <select name="children">
                @for($i=0; $i<=4; $i++)
                    <option value="{{ $i }}" {{ request('children', 0) == $i ? 'selected' : '' }}>{{ $i }} Child(ren)</option>
                @endfor
            </select>
        </div>

        <div class="search-field">
            <label>Total Rooms</label>
            <select name="total_rooms">
                @for($i=1; $i<=5; $i++)
                    <option value="{{ $i }}" {{ request('total_rooms', 1) == $i ? 'selected' : '' }}>{{ $i }} Room(s)</option>
                @endfor
            </select>
        </div>

        <div class="search-field d-flex align-items-end">
            <button type="submit" class="btn-search" style="width:100%; height: 46px;">Find Rooms</button>
        </div>

    </form>
</div>

<style>
/* Adjust grid for new fields */
.search-card {
    grid-template-columns: repeat(4, 1fr);
}
@media(max-width:992px){ 
    .search-card { grid-template-columns: repeat(2, 1fr); } 
}
@media(max-width:520px){ 
    .search-card { grid-template-columns: 1fr; } 
}
</style>
