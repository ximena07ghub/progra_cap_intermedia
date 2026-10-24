document.addEventListener('DOMContentLoaded', async () => {
    const formulario = document.querySelector('#form-perfil');

    if (!formulario) return;

    const mensaje = formulario.querySelector('[data-perfil-mensaje]');
    const boton = formulario.querySelector('button[type="submit"]');
    const ruta = formulario.dataset.api;

    let token = '';
    boton.disabled = true;

    try {
        const respuesta = await fetch(ruta, {
            credentials: 'same-origin'
        });

        const datos = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(datos.mensaje || 'No fue posible cargar el perfil.');
        }

        formulario.elements.nombre.value = datos.perfil.nombre;
        formulario.elements.email.value = datos.perfil.email;
        token = datos.csrf_token;

        boton.disabled = false;
    } catch (error) {
        mensaje.textContent = error.message;
    }

    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();

        if (!token) {
            mensaje.textContent = 'Recarga la página para cargar tu perfil.';
            return;
        }

        boton.disabled = true;
        mensaje.textContent = 'Guardando...';

        try {
            const respuesta = await fetch(ruta, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': token
                },
                body: JSON.stringify({
                    nombre: formulario.elements.nombre.value,
                    email: formulario.elements.email.value
                })
            });

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                throw new Error(datos.mensaje || 'No fue posible guardar.');
            }

            mensaje.textContent = datos.mensaje;
        } catch (error) {
            mensaje.textContent = error.message;
        } finally {
            boton.disabled = false;
        }
    });
});