document.addEventListener('DOMContentLoaded', () => {
    const payBtn = document.getElementById('proceed-payment');
    const guestEmailInput = document.getElementById('guest-email');
    const guestEmailConfirmInput = document.getElementById('guest-email-confirm');
    const ticketQuantityDisplay = document.getElementById('ticket-quantity-display');
    const spinnerContainer = document.querySelector('.custom-spinner');

    const seatPrice = parseFloat(spinnerContainer?.dataset.price || 0);
    const eventId = new URLSearchParams(window.location.search).get('id');

    async function handlePayment() {
        const email = guestEmailInput.value.trim();
        const confirmEmail = guestEmailConfirmInput.value.trim();
        const ticketQuantity = parseInt(ticketQuantityDisplay.textContent, 10);
        const totalPrice = (ticketQuantity * seatPrice).toFixed(2);

        if (!email || email !== confirmEmail || ticketQuantity <= 0) {
            alert('Por favor, verifica los datos ingresados.');
            return;
        }

        try {
            const response = await fetch('../app/controllers/guest_checkout_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email,
                    ticketQuantity,
                    totalPrice,
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

    payBtn.addEventListener('click', handlePayment);
});