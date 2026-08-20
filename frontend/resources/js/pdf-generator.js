import { jsPDF } from "jspdf";
import "jspdf-autotable";

export const generateFactura = (facturaData) => {
    const doc = new jsPDF();
    
    // Header
    doc.setFillColor(227, 0, 27); // Fritolay Red #E3001B
    doc.rect(0, 0, 210, 40, 'F');
    
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(22);
    doc.text("Fritolay Ambato", 14, 25);
    
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(16);
    doc.text(`Factura N°: ${facturaData.numero}`, 14, 50);
    
    doc.setFontSize(12);
    doc.text(`Cliente: ${facturaData.clienteNombre}`, 14, 60);
    doc.text(`Fecha: ${facturaData.fecha}`, 14, 70);

    const tableColumn = ["Producto", "Cantidad", "P. Unitario", "Total"];
    const tableRows = facturaData.items.map(item => [
        item.nombre,
        item.cantidad,
        `$${item.precioUnitario}`,
        `$${(item.cantidad * item.precioUnitario).toFixed(2)}`
    ]);

    doc.autoTable({
        head: [tableColumn],
        body: tableRows,
        startY: 80,
        headStyles: { fillColor: [245, 197, 24] }, // Fritolay Yellow #F5C518
    });

    const finalY = doc.lastAutoTable.finalY || 80;
    doc.text(`Total a Pagar: $${facturaData.total}`, 14, finalY + 10);

    doc.save(`factura_${facturaData.numero}.pdf`);
};

export const generateGuiaRemision = (guiaData) => {
    const doc = new jsPDF();
    doc.text("Guía de Remisión - Fritolay", 14, 20);
    // similar structure to factura
    doc.save(`guia_remision_${guiaData.id}.pdf`);
};

export const generateGuiaRuta = (guiaData) => {
    const doc = new jsPDF();
    doc.text("Guía de Ruta - Fritolay", 14, 20);
    // similar structure with list of businesses
    doc.save(`guia_ruta_${guiaData.id}.pdf`);
};
