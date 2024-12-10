console.log('SOY EL SCRIPT.JS Y ESTOY CARGADO');
console.log('Documento cargado');

const generatePdfButton = document.getElementById('generate-pdf');
console.log('Botón:', generatePdfButton);

if (generatePdfButton) {
    generatePdfButton.addEventListener('click', () => {
        console.log('Botón clickeado');

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        const infoplatoContainer = document.getElementById('plato-info-container');
        console.log('Contenedor:', infoplatoContainer);

        if (infoplatoContainer) {
            // Creamos un nuevo contenedor HTML para el PDF
            const pdfContent = document.createElement('div');
            pdfContent.setAttribute('id', 'pdf-content');

            // Agregamos la imagen como encabezado
            const imagenHeader = document.createElement('img');
            imagenHeader.src = 'images/eatrightfood.jpg';
            imagenHeader.style.width = '100px'; // Ajustamos el tamaño de la imagen
            imagenHeader.style.display = 'block'; // La hacemos un bloque para centrarla
            imagenHeader.style.margin = '0 auto'; // Centramos la imagen
            pdfContent.appendChild(imagenHeader);

            // Clonamos el contenido del contenedor original
            const clonedContainer = infoplatoContainer.cloneNode(true);
            const imagesInContainer = clonedContainer.querySelectorAll('img'); // Seleccionamos todas las imágenes dentro del contenedor clonado

            // Ajustamos el tamaño de todas las imágenes encontradas
            imagesInContainer.forEach(image => {
                image.style.width = '150px'; // Cambiamos el tamaño de la imagen
            });

            pdfContent.appendChild(clonedContainer);

            // Agregamos el pie de página al final del contenedor
            const footerText = document.createElement('div');
            footerText.textContent = 'EAT RIGHT FOOD - Todos los derechos reservados';
            pdfContent.appendChild(footerText);

            // Adjuntamos el contenedor al DOM temporalmente para asegurarnos de que los estilos se apliquen
            document.body.appendChild(pdfContent);

            // Generamos el PDF con el contenido del nuevo contenedor
            doc.html(pdfContent, {
                callback: function (doc) {
                    console.log('PDF generado');
                    doc.save('plato.pdf');

                    // Eliminamos el contenedor del DOM después de generar el PDF
                    document.body.removeChild(pdfContent);
                },
                x: 10,
                y: 10,
                width: 180, // Ajusta el ancho del contenido
                windowWidth: document.body.scrollWidth,
                autoPaging: 'text',
                html2canvas: {
                    scale: 0.3, // Ajustamos la escala para mejor calidad
                    useCORS: true // Aseguramos que se manejen las políticas de CORS
                }
            });
        } else {
            console.error('No se encontró el contenedor de platos.');
        }
    });
} else {
    console.error('No se encontró el botón de generar PDF.');
}