<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- ICONO -->
<link rel="icon" type="image/png" href="assets/img/icono.png">

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="author" content="Estudiantes de la UPTAEB">
<meta name="keywords" content="maquillaje, belleza, cosméticos, cuidado personal, Lovemakeup, productos de belleza, maquillaje online, tienda de belleza">
<meta name="description" content="Lovemakeup, tu tienda en línea de productos de belleza y maquillaje de alta calidad. Descubre nuestras ofertas en cosméticos y cuidado personal.">

<!-- Google Fonts Plus -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="assets/fontawesome/css/all.min.css" />

<!-- Estilos y Libreria  -->
<link rel="stylesheet" href="assets/css/catalogo.css">
<link rel="stylesheet" href="assets/driver.js/dist/driver.css">    

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    brand: {
                        50: '#fdf2f8',
                        100: '#fce7f3',
                        200: '#fbcfe8',
                        300: '#f472b6',
                        400: '#e879f9',
                        500: '#ec4899',
                        600: '#db2777',
                        700: '#be185d',
                        800: '#9d174d',
                        900: '#831843',
                    },
                    accent: {
                        fuchsia: '#d81b60',
                        rose: '#e91e63',
                        pinkDark: '#2d0c1e'
                    }
                },
                fontFamily: {
                    sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                }
            }
        }
    }
</script>

<!-- Script de librerias -->
<script src="assets/js/libreria/moment.js"></script>  
<script src="assets/js/libreria/sweetalert2.js"></script>
<script src="assets/js/catalago/jquery-1.11.0.min.js"></script>
<script src="assets/driver.js/dist/driver.js.iife.js"></script>


<script>
    window.addEventListener('load', function() {
    const loader = document.getElementById('app-loader');
        if (loader) {
            loader.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => {
                    loader.remove(); // Elimina el elemento del DOM tras el desvanecimiento
                }, 500);
        }
    });
</script>