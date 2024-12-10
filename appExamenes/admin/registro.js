function enviarFormulario() {
    const form = document.getElementById('miFormulario');
    const formData = new FormData(form);
    console.log(formData);
    fetch('http://gpi1nashe.infinityfreeapp.com/appExamenes/admin/insertaUsuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const respuestaDiv = document.getElementById('respuesta');
        if (data.status === 'success') {
            respuestaDiv.innerText = data.message;
            respuestaDiv.style.color = 'green';
        } else {
            respuestaDiv.innerText = data.message;
            respuestaDiv.style.color = 'red';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('respuesta').innerText = 'Ocurrió un error al enviar el formulario.';
        document.getElementById('respuesta').style.color = 'red';
    });
}
