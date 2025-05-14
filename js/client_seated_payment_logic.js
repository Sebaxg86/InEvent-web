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
                alert('Compra realizada con éxito. Revisa tu correo para más detalles.');
                window.location.href = `/confirmation.php?order_id=${result.orderId}`;
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