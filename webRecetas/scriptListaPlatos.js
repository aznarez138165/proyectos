console.log('SOY EL SCRIPT.JS Y ESTOY CARGADO');
console.log('Documento cargado');


// Selecciona todos los botones de generación de PDF
const generatePdfButtons = document.querySelectorAll('.generate-pdf-button');

// Itera sobre cada botón y agrega un controlador de eventos para generar el PDF
generatePdfButtons.forEach(function (button) {
    button.addEventListener('click', () => {
        console.log('Botón clickeado');

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

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

        // Agregamos la línea "Listado de platos:"
        const listadoPlatos = document.createElement('div');
        listadoPlatos.textContent = 'Listado de platos:';
        listadoPlatos.style.fontSize = '14px'; // Ajustamos el tamaño del texto
        listadoPlatos.style.fontWeight = 'bold'; // Hacemos el texto en negrita
        listadoPlatos.style.marginBottom = '10px'; // Agregamos un margen inferior
        pdfContent.appendChild(listadoPlatos);

        // Clonamos el contenido del contenedor original
        const platosContainer = document.getElementById('platos-container');
        if (platosContainer) {
            const clonedContainer = platosContainer.cloneNode(true);
            clonedContainer.style.fontSize = '10px';

            pdfContent.appendChild(clonedContainer);
        } else {
            console.error('No se encontró el contenedor de platos.');
        }

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
    });
});


/*
console.log('SOY EL SCRIPT.JS Y ESTOY CARGADO');
console.log('Documento cargado');

const generatePdfButton = document.getElementById('generate-pdf');
console.log('Botón:', generatePdfButton);

if (generatePdfButton) {
    generatePdfButton.addEventListener('click', () => {
        console.log('Botón clickeado');

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

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

        // Agregamos la línea "Listado de platos:"
        const listadoPlatos = document.createElement('div');
        listadoPlatos.textContent = 'Listado de platos:';
        listadoPlatos.style.fontSize = '14px'; // Ajustamos el tamaño del texto
        listadoPlatos.style.fontWeight = 'bold'; // Hacemos el texto en negrita
        listadoPlatos.style.marginBottom = '10px'; // Agregamos un margen inferior
        pdfContent.appendChild(listadoPlatos);

        // Clonamos el contenido del contenedor original
        const platosContainer = document.getElementById('platos-container');
        if (platosContainer) {
            const clonedContainer = platosContainer.cloneNode(true);
            clonedContainer.style.fontSize = '10px';

            pdfContent.appendChild(clonedContainer);
        } else {
            console.error('No se encontró el contenedor de platos.');
        }

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
    });
} else {
    console.error('No se encontró el botón de generar PDF.');
}
*/