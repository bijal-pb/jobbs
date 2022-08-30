<div>
@if($updateMode)
      @include('admin.document-type.edit')
    @elseif($createMode)
    @include('admin.document-type.create')
  @else
  
            <a class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 border border-transparent bg-blue-500 rounded-lg active:bg-blue-500 focus:outline-none focus:shadow-outline-blue float-right cursor-pointer"  wire:click="create()">
                <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" aria-hidden="true" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clipRule="evenodd" />
                </svg>
                <span>Add Document Type</span>
            </a>
        
    <div class="row mb-4 text-gray-600 dark:text-gray-300">         
        <div class="flex space-x-2" style="display: inline-flex;">
          <!-- <div class="col form-inline" style="padding: 6px;">
            Per Page: &nbsp;
            <select wire:model="perPage" class="form-control text-gray-600 border-b">
                <option>5</option>
                <option>10</option>
                <option>15</option>
                <option>25</option>
             </select>
          </div> -->

          <!-- <a href="#" type="button" class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 border border-transparent bg-blue-500 rounded-lg active:bg-blue-500 hover:bg-blue-500 focus:outline-none focus:shadow-outline-blue float-left cursor-pointer"
            onclick="confirm('Are you sure you want to Export these records?') || event.stopImmediatePropagation()"
            wire:click="exportSelected()">
            Export
          </a>

          <a href="#" type="button" class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 border border-transparent bg-blue-500 rounded-lg active:bg-blue-500 hover:bg-blue-500 focus:outline-none focus:shadow-outline-blue float-left cursor-pointer"
            onclick="confirm('Are you sure you want to PDF these records?') || event.stopImmediatePropagation()"
            wire:click="pdfexport()">
            PDF
            </a> -->

          <a href="#" type="button" class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 border border-transparent bg-blue-500 rounded-lg active:bg-blue-500 hover:bg-blue-500 focus:outline-none focus:shadow-outline-blue float-left cursor-pointer"
            onclick="confirm('Are you sure you want to CSV these records?') || event.stopImmediatePropagation()"
            wire:click="csvexport()">
            CSV
          </a>
        </div>
</br></br>
         <div class="col float-right" style="margin-top: 3px; border: 2px solid #a5a7a7; border-radius:5px">
            <input wire:model.debounce.300ms="search" class="form-control border-b float-right" style="border-radius:5px" type="text" placeholder="Search ID,Name...">
        </div>

    </div>
</br>
    <div class="w-full mb-8 overflow-hidden rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full table-fixed data-table dark:text-gray-200" style="color:unset; width:100%">
              <thead>
                <tr
                    class="text-xs font-semibold  tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800 cursor-pointer">
                    <th wire:click="sortBy('id')" class="px-4 py-3 cursor-pointer">ID <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="15px" height="15px" style="display: inline;" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg></th>
                    <th wire:click="sortBy('name')" class="cursor-pointer"> Name <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="15px" height="15px" style="display: inline;" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg></th>
                    <th wire:click="sortBy('description')" class="px-4 py-3 cursor-pointer"> Description 
                    <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="15px" height="15px" style="display: inline;" fill="currentColor"> -->
                      <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    <!-- </svg> -->
                    </th>
                    <th>Action</th>
                </tr>
            </thead>  
                <tbody class="bg-transperent divide-y dark:divide-gray-700">
                    @foreach ($doctypes as $doctype)
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <td class="px-4 py-3 break-all"> {{ $doctype->id }} </td>
                            <td class=" break-all"> {{ $doctype->name }} </td>
                            <td class="px-4 py-3"><textarea disabled width="150" height="150">{{ $doctype->description }}</textarea></td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-1">&nbsp;&nbsp;
                                <button wire:click="edit({{ $doctype->id }})" id="edit" class="p-1 text-blue-600 hover:bg-blue-600 hover:text-white rounded">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                                  </button>
                                    <button wire:click="confirmDelete({{ $doctype->id }})" class="deleteContent p-1 text-red-600 hover:bg-red-600 hover:text-white rounded">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                   
                                </div>
                            </td>
                            {{-- <td class="px-4 py-3">
                              <div class="mr-2">
                                <a class="flex items-center justify-between px-1 float-left py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-green-400 border border-transparent rounded-lg active:bg-green-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple cursor-pointer" href="{{ route('content-session.view') }}">
                                    <span>Sessions</span>
                                </a>
                              </div>
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class=" dark:bg-gray-800  dark:text-gray-200 rounded-lg rounded-t-none max-w-screen rounded-lg border-b border-gray-200 bg-white">
              <div class="dark:bg-gray-800 p-2 sm:flex items-center justify-between">
                <div class="dark:bg-gray-800 my-2 sm:my-0 flex items-center">
                  <select id="perPage" class="per-page dark:bg-gray-800 mt-1 form-select block w-full pl-3 pr-10 py-2 text-base leading-6 border-gray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5" wire:model="perPage">
                      <option value="5">5</option>
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                  </select>
                </div>

                <div class="dark:bg-gray-800 my-4 sm:my-0">
                  <div  class="dark:bg-gray-800 lg:flex justify-center">
                      <span class="dark:bg-gray-800">{{ $doctypes->links('livewire.datatables.tailwind-pagination') }}</span>
                  </div>
                </div>

                <div class="flex justify-end text-gray-600 dark:bg-gray-800">
                Document Types {{ $doctypes->firstItem() }} - {{ $doctypes->lastItem() }} of
                  {{ $doctypes->total() }}
                </div>
              </div>
            </div>
        </div>
    </div>
    
      <!-- modal div -->
      @if($deleteModel == true)
      <div class="mt-6">
        <!-- Dialog (full screen) -->
        <div class="absolute top-0 left-0 flex items-center justify-center w-full h-full" style="background-color: rgba(0,0,0,.5);" x-show="open"  >
  
          <!-- A basic modal dialog with title, body and one button to close -->
          <div class="h-auto p-4 mx-2 text-left bg-white rounded shadow-xl md:max-w-xl md:p-6 lg:p-8 md:mx-0" @click.away="open = false">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg font-medium leading-6 text-gray-900">
              Are you sure want to delete this document type? 
              </h3>
  
              <div class="mt-2">
                <p class="text-sm leading-5 text-gray-500">
                </p>
            </div>
          </div>
  
            <!-- One big close button.  --->
            <div class="mt-5 sm:mt-6">
              <span class="flex rounded-md shadow-sm">
                <button wire:click="closeDeleteModel()" class="inline-flex justify-center w-full px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
                  Cancel!
                </button>
                <button wire:click="documenttypeDelete()" class="inline-flex ml-4 justify-center w-full px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">
                    Delete!
                </button>
              </span>
            </div>
  
          </div>
        </div>
      </div>
      @endif
      
      {{-- <script>
        $(document).click(function () {
          alert();
          const params = new URLSearchParams(window.location.search)
          let pageNo = params.get('page');
          alert(pageNo)
      });
    </script> --}}
    
      @endif 
</div>


