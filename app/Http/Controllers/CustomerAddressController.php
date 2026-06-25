<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerAddressController extends Controller
{
    public function index(Request $request): View
    {
        return view('customer.addresses.index', [
            'addresses' => $request->user()->addresses()->latest('is_default')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('customer.addresses.create', [
            'address' => new Address(['is_default' => false]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        DB::transaction(function () use ($request, $validated): void {
            if ($validated['is_default']) {
                $request->user()->addresses()->update(['is_default' => false]);
            }

            $request->user()->addresses()->create($validated);
        });

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Address saved.');
    }

    public function edit(Request $request, Address $address): View
    {
        $this->authorizeAddress($request, $address);

        return view('customer.addresses.edit', [
            'address' => $address,
        ]);
    }

    public function update(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeAddress($request, $address);

        $validated = $this->validated($request);

        DB::transaction(function () use ($request, $address, $validated): void {
            if ($validated['is_default']) {
                $request->user()->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
            }

            $address->update($validated);
        });

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Address updated.');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeAddress($request, $address);

        $address->delete();

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Address deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:80'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $validated['is_default'] = $request->boolean('is_default');

        return $validated;
    }

    private function authorizeAddress(Request $request, Address $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 403);
    }
}
