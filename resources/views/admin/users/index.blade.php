
<x-app-layout title="Users">
    <div class="container grid px-6 mx-auto">
        <h6 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            User Management
        </h6>
        {{-- <div class="mb-2">
            <a class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 border border-transparent bg-blue-500 rounded-lg active:bg-blue-500 focus:outline-none focus:shadow-outline-blue float-right cursor-pointer" href="{{ route('users.create') }}">
                <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" aria-hidden="true" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clipRule="evenodd" />
                </svg>
                <span>User</span>
            </a>
        </div> --}}
        
        <livewire:user-datatable exportable/>
    </div>
    <script type="text/javascript">
        function closeAlert(event){
            let element = event.target;
            while(element.nodeName !== "BUTTON"){
                element = element.parentNode;
            }
            element.parentNode.parentNode.removeChild(element.parentNode);
        }
    </script>
    @if(Session::has('message'))
    <script>
        $(function(){
                toastr.success("{{ Session::get('message') }}");
            })
    </script>
    @endif
</x-app-layout>