document.addEventListener('DOMContentLoaded', () => {
    const payBtn = document.getElementById('proceed-payment');
    const eventId = new URLSearchParams(window.location.search).get('id'); // Obtener el ID del evento desde la URL

    async function handlePayment() {
        if (window.selectedSeats.length === 0) {
            alert('Por favor, selecciona al menos un asiento.');
            return;
        }

        try {
            // Enviar los datos al servidor
            const response = await fetch('../app/controllers/client_seated_checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    selectedSeats: window.selectedSeats, // Usar la variable global para los asientos seleccionados
                    totalPrice: window.totalPrice, // Usar la variable global para el precio total
                    eventId,
                }),
            });

            const result = await response.json();

            if (response.ok) {
                // Mostrar la pantalla de espera
                document.body.innerHTML = `
                    <div style="text-align: center; margin-top: 20%;">
                        <h2>Por favor, complete el pago en PayPal. No cierre esta ventana.</h2>
                        <div style="margin: 20px auto; width: 50px; height: 50px; border: 5px solid #ccc; border-top: 5px solid #007bff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    </div>
                    <style>
                        @keyframes spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                    </style>
                `;
                // Redirigir a PayPal
                window.location.href = result.approvalUrl;
            } else {
                alert(`Error: ${result.message}`);
            }
        } catch (error) {
            console.error('Error al procesar el pago:', error);
            alert('Ocurrió un error al procesar el pago. Inténtalo de nuevo más tarde.');
        }
    }

    // Asociar el evento de clic al botón de pago
    payBtn.addEventListener('click', handlePayment);
});