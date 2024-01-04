<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Your head content goes here -->
</head>

<body>
    @yield('content')
    {{ $slot }}

    @livewire('notifications')
</body>

</html>
