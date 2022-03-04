<div>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-600 leading-right">
                Toda la información de productos PRACTICA
            </h2>
            <x-button-link class="ml-auto" href="{{ route('admin.products.create') }}">
                Agregar producto
            </x-button-link>
        </div>
    </x-slot>

    <x-table-responsive>
        <div class="px-6 pt-4">
            <x-jet-input class="w-full" wire:model="search" type="text" placeholder="Buscador" />

        </div>

        <div class="flex px-6 py-3">
            <div x-data="{ dropdownPaginas: false }" @click.away="dropdownPaginas = false">
                <x-button-link class="ml-auto" @click="dropdownPaginas = !dropdownPaginas">
                    Paginación
                </x-button-link>

                <div x-show="dropdownPaginas">
                    <select wire:model="pagination" class="absolute bg-gray-100 rounded-md shadow-xl">
                        <option value="5">5</option>
                        <option value="15">15</option>
                        <option value="35">35</option>
                        <option value="30">30</option>
                    </select>
                </div>
            </div>

            <div x-data="{ dropdownColumnas: false }" @click.away="dropdownColumnas = false" class="ml-3">
                <x-button-link class="ml-auto" @click="dropdownColumnas = !dropdownColumnas">
                    Columnas
                </x-button-link>

                <div x-show="dropdownColumnas" class="absolute w-32 bg-gray-100 rounded-md shadow-xl">
                    <span class="block text-xs">
                        @foreach ($columns as $column)
                            <input type="checkbox" wire:model="selectedColumns" value="{{ $column }}">
                            <label>{{ $column }}</label>
                            <br />
                        @endforeach
                    </span>
                </div>
            </div>


            <div x-data="{ dropdownPrecio: false }" @click.away="dropdownPrecio = false" class="ml-3">
                <x-button-link class="ml-auto" @click="dropdownPrecio = !dropdownPrecio">
                    Precio
                </x-button-link>

                <div x-show="dropdownPrecio" class="absolute w-80 bg-gray-100 rounded-md p-2 shadow-lg">
                    <h4 class="text-lg">Precio</h4>
                    <div class="flex items-center mb-2">
                        <label for="minPrice" class="mr-3">Min</label>
                        <x-jet-input id="minPrice" class="w-24" wire:model="minPrice" type="number"
                            placeholder="Min" />
                    </div>
                    <div class="flex items-center">
                        <label for="maxPrice" class="mr-2">Max</label>
                        <x-jet-input id="maxPrice" class="w-24" wire:model="maxPrice" type="number"
                            placeholder="Max" />
                    </div>
                </div>
            </div>





            <div class="bg-gray-100 shadow-lg  rounded ">

            </div>

        </div>
</div>




@if ($products->count())
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nombre
                    <button wire:click="sortCol('name', 'asc')">
                        <span class="fa fa-arrow-up"></span>
                    </button>
                    <button wire:click="sortCol('name', 'desc')">
                        <span class="fa fa-arrow-down"></span>
                    </button>
                </th>
                @if ($this->showColumn('Categoría'))
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Categoría
                    </th>
                @endif
                @if ($this->showColumn('Estado'))
                    <th scope="col"
                        class=" text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado

                    </th>
                @endif
                @if ($this->showColumn('Precio'))
                    <th scope="col"
                        class="px-8 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Precio
                        <button wire:click="sortCol('price', 'asc')">
                            <span class="fa fa-arrow-up"></span>
                        </button>
                        <button wire:click="sortCol('price', 'desc')">
                            <span class="fa fa-arrow-down"></span>
                        </button>
                    </th>
                @endif
                @if ($this->showColumn('Marca'))
                    <th scope="col"
                        class="px-8 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Marca
                    </th>
                @endif
                @if ($this->showColumn('Stock'))
                    <th scope="col"
                        class="px-14 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                        Stock
                        <button wire:click="sortCol('quantity', 'asc')">
                            <span class="fa fa-arrow-up"></span>
                        </button>
                        <button wire:click="sortCol('quantity', 'desc')">
                            <span class="fa fa-arrow-down"></span>
                        </button>
                    </th>
                @endif
                @if ($this->showColumn('Colores'))
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                        Colores
                    </th>
                @endif
                @if ($this->showColumn('Tallas'))
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                        Tallas
                    </th>
                @endif
                @if ($this->showColumn('Fecha de creación'))
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                        Fecha de creación
                        <button wire:click="sortCol('created_at', 'asc')">
                            <span class="fa fa-arrow-up"></span>
                        </button>
                        <button wire:click="sortCol('created_at', 'desc')">
                            <span class="fa fa-arrow-down"></span>
                        </button>
                    </th>
                @endif
                @if ($this->showColumn('Fecha de edición'))
                    <th scope="col"
                        class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                        Fecha de edición
                        <button wire:click="sortCol('updated_at', 'asc')">
                            <span class="fa fa-arrow-up"></span>
                        </button>
                        <button wire:click="sortCol('updated_at', 'desc')">
                            <span class="fa fa-arrow-down"></span>
                        </button>
                    </th>
                @endif
                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Editar</span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($products as $product)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 object-cover">
                                <img class="h-10 w-10 rounded-full"
                                    src="{{ $product->images->count() ? Storage::url($product->images->first()->url) : 'img/default.jpg' }}"
                                    alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $product->name }}
                                </div>
                            </div>
                        </div>
                    </td>
                    @if ($this->showColumn('Categoría'))
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->subcategory->category->name }}
                            </div>
                            <div class="text-sm text-gray-500">{{ $product->subcategory->name }}</div>
                        </td>
                    @endif
                    @if ($this->showColumn('Estado'))
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-$product->status == 1 ? 'red' : 'green' }}-100 text-{{ $product->status == 1 ? 'red' : 'green' }}-800">
                                {{ $product->status == 1 ? 'Borrador' : 'Publicado' }}
                            </span>
                        </td>
                    @endif
                    @if ($this->showColumn('Precio'))
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->price }} &euro;
                        </td>
                    @endif
                    @if ($this->showColumn('Marca'))
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->brand->name }}
                        </td>
                    @endif
                    @if ($this->showColumn('Stock'))
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            @if (is_null($product->quantity))
                                @if ($product->colors->count())
                                    {{ array_sum($product->colors->pluck('pivot')->pluck('quantity')->all()) }}
                                @else
                                    {{ array_sum($product->sizes->pluck('colors')->collapse()->pluck('pivot')->pluck('quantity')->all()) }}
                                @endif
                            @else
                                {{ $product->quantity }}
                            @endif
                        </td>
                    @endif
                    @if ($this->showColumn('Colores'))
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ implode(', ', $product->colors->pluck('name')->all()) }}
                        </td>
                    @endif
                    @if ($this->showColumn('Tallas'))
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ implode(', ', $product->sizes->pluck('name')->all()) }}
                        </td>
                    @endif
                    @if ($this->showColumn('Fecha de creación'))
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->created_at }}
                        </td>
                    @endif
                    @if ($this->showColumn('Fecha de edición'))
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->updated_at }}
                        </td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.products.edit', $product) }}"
                            class="text-indigo-600 hover:text-indigo-900">Editar</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="px-6 py-4">
        No existen productos coincidentes
    </div>
@endif
@if ($products->hasPages())
    <div class="px-6 py-4">
        {{ $products->links() }}
    </div>
@endif
</x-table-responsive>
</div>
