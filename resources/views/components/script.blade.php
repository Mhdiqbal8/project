<!-- Core -->
<script src="{{ url('/assets/vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ url('/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url('/assets/vendor/js-cookie/js.cookie.js') }}"></script>
<script src="{{ url('/assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ url('/assets/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js') }}"></script>
<!-- Argon JS -->
<script src="{{ url('/assets/js/argon.js?v=1.2.0') }}"></script>

<!-- Data Tables random resource -->
{{-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.js"></script> --}}

{{-- Data tables bootstrap 4 --}}
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>

<script>
  $(document).ready(function(){
      $('.table-data').DataTable();
  });
</script>


