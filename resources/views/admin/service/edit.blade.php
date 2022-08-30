<div class="mb-2">
            <a class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-500 border border-transparent rounded-lg active:bg-blue-500 hover:bg-blue-500 focus:outline-none focus:shadow-outline-blue float-right cursor-pointer"  wire:click="cancel()">
                <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" aria-hidden="true" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clipRule="evenodd" />
                </svg>
                <span>Back</span>
            </a>
        </div>
</br></br>
<form wire:submit.prevent="update" enctype="multipart/form-data">
<div>
    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <label class="block text-sm">
            <span class="text-gray-700 dark:text-gray-400">Name</span>
            <input id="name" name="name" wire:model="name" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:text-gray-300 dark:focus:shadow-outline-gray form-input" />
            @error('name') <span class="error text-red-500 text-xs italic">{{ $message }}</span> @enderror
        </label>
        <label class="block text-sm">
            <span class="text-gray-700 dark:text-gray-400">Icon</span>
            <input id="icon" name="img" type="file" wire:model="icon" enctype="multipart/form-data" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:text-gray-300 dark:focus:shadow-outline-gray form-input" />
       </label>
        <label class="block text-sm">
            <span class="text-gray-700 dark:text-gray-400">Detail</span>
            <textarea id="detail"  name="detail" wire:model="detail" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:text-gray-300 dark:focus:shadow-outline-gray form-input"></textarea>
        </label>
        
        <div class="flex flex-wrap py-2 items-center justify-evenly">
            <div class="sm:w-full md:w-1/2 lg:w-1/2 xl:w-1/2 h-12 text-center align-baseline">
                <button class="bg-blue-500 text-white cursor-pointer  py-2 px-4 rounded" type="submit"> Update</button>
                <a class="inline-block align-baseline bg-blue-500 text-white py-2 px-4 rounded cursor-pointer" wire:click="cancel()">
                    Cancel
                </a>
            </div>
        </div>
</div>
<form>
