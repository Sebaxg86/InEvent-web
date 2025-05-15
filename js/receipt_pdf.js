document.addEventListener('DOMContentLoaded', () => {
    const savePdfBtn = document.getElementById('save-pdf');

    savePdfBtn.addEventListener('click', async () => {
        const element = document.querySelector('.container'); // Selecciona el contenedor principal

        // Usa html2canvas para capturar el contenedor como imagen
        const canvas = await html2canvas(element, {
            scale: 2, // Aumenta la resolución de la captura
            useCORS: true // Permite cargar imágenes externas
        });

        // Convierte el canvas a una imagen en formato PNG
        const imgData = canvas.toDataURL('image/png');

        // Crea un nuevo PDF con jsPDF
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'px',
            format: [canvas.width, canvas.height] // Ajusta el tamaño del PDF al tamaño del canvas
        });

        // Agrega la imagen al PDF
        pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);

        // Descarga el PDF
        pdf.save('Receipt.pdf');
    });
});