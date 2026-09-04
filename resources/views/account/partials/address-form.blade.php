@php($editing = !empty($address))
<form method="POST" action="{{ $editing ? route('account.addresses.update', $address) : route('account.addresses.store') }}" class="space-y-4">
    @csrf
    @if($editing) @method('PUT') @endif
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block"><span class="mb-1.5 block text-xs font-black text-slate-600">Label</span><input name="label" value="{{ old('label', $address->label ?? 'Home') }}" required maxlength="50" placeholder="Home / Office" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"></label>
        <label class="block"><span class="mb-1.5 block text-xs font-black text-slate-600">Recipient name</span><input name="recipient_name" value="{{ old('recipient_name', $address->recipient_name ?? auth()->user()->name) }}" required maxlength="150" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"></label>
        <label class="block"><span class="mb-1.5 block text-xs font-black text-slate-600">Phone</span><input name="phone" value="{{ old('phone', $address->phone ?? auth()->user()->phone) }}" required maxlength="50" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"></label>
        <label class="block"><span class="mb-1.5 block text-xs font-black text-slate-600">City</span><input name="city" value="{{ old('city', $address->city ?? '') }}" required maxlength="100" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"></label>
    </div>
    <label class="block"><span class="mb-1.5 block text-xs font-black text-slate-600">Address</span><textarea name="address" rows="3" required maxlength="1000" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500">{{ old('address', $address->address ?? '') }}</textarea></label>
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block"><span class="mb-1.5 block text-xs font-black text-slate-600">Postal code</span><input name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" maxlength="50" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"></label>
        <label class="block"><span class="mb-1.5 block text-xs font-black text-slate-600">Country</span><input name="country" value="{{ old('country', $address->country ?? 'Bangladesh') }}" required maxlength="100" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-500"></label>
    </div>
    <label class="flex items-center gap-2 text-xs font-bold text-slate-600"><input type="checkbox" name="is_default" value="1" {{ old('is_default', $address?->is_default ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Set as default address</label>
    <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-black text-white hover:bg-indigo-700">{{ $editing ? 'Save address' : 'Add address' }}</button>
</form>
