<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Product') }}
      </h2>
  </x-slot>

  <x-slot name="script">
    <script>
      // AJAX DataTables

      var datatable = $('#crudTable').DataTable({
        ajax: {
          url: '{!! url()->current() !!}'
        },
        columns: [
          { data: 'id', name:'id' , width: '5%'},
          { data: 'name', name:'name'},
          { data: 'price', name:'price'},
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            width: '25%'
          }
        ]
      })
    </script>
    <!-- Di bagian bawah Blade Template -->
<!-- Di bagian bawah Blade Template -->
<script>
  // Misalkan Anda menggunakan JavaScript untuk menangani form submission
  document.getElementById('product-form').addEventListener('submit', function (event) {
      event.preventDefault();

      // Ambil nilai dari input nama dan harga produk
      var name = document.getElementById('name').value;
      var price = document.getElementById('price').value;

      // Siapkan data yang akan dikirimkan ke controller
      var formData = {
          name: name,
          price: price
          // Tambahkan atribut lainnya ke dalam formData jika ada
      };

      // Kirim data ke controller melalui AJAX menggunakan Axios
      axios.post('/product', formData)
          .then(function (response) {
              // Menampilkan SweetAlert dengan data yang diterima dari controller
              Swal.fire(response.data);

              // Redirect ke halaman index setelah menampilkan SweetAlert
              window.location.href = '/dashboard/product';
          })
          .catch(function (error) {
              console.error(error);
          });
  });
</script>

  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-10">
          <a href="{{ route('dashboard.product.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-lg">
          + Create Product
          </a>
        </div>
        <div class="shadow overflow-hidden sm-rounded-md">
          <div class="px-4 py-5 bg-white sm:p-6">
            <table id="crudTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama</th>
                  <th>Harga</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
    </div>
</div>
</x-app-layout>
