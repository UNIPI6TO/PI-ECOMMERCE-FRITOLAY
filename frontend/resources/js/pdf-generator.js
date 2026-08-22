import { jsPDF } from "jspdf";
import "jspdf-autotable";

export const generateFactura = (facturaData) => {
    const doc = new jsPDF();
    
    // Header - Logo/Empresa
    doc.setFillColor(227, 0, 27); // Fritolay Red #E3001B
    doc.rect(0, 0, 210, 35, 'F');
    
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(24);
    doc.setFont("helvetica", "bold");
    doc.text("FRITOLAY AMBATO", 14, 23);
    
    // Cuadro SRI (Arriba a la derecha)
    doc.setFillColor(255, 255, 255);
    doc.roundedRect(110, 10, 90, 30, 2, 2, 'F');
    doc.setDrawColor(200, 200, 200);
    doc.roundedRect(110, 10, 90, 30, 2, 2, 'S');
    
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text("RUC: 1890000000001", 115, 18);
    doc.setFontSize(12);
    doc.setFont("helvetica", "bold");
    doc.text("FACTURA", 115, 25);
    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text(`No. ${facturaData.numero}`, 115, 32);
    
    // Datos de la empresa
    doc.setFontSize(9);
    doc.text("Dirección Matriz: Av. Los Guaytambos y Montalvo", 14, 45);
    doc.text("OBLIGADO A LLEVAR CONTABILIDAD: SÍ", 14, 50);

    // Datos del Cliente (Recuadro)
    doc.roundedRect(14, 55, 186, 30, 2, 2, 'S');
    doc.setFont("helvetica", "bold");
    doc.text("Razón Social / Nombres y Apellidos:", 17, 62);
    doc.setFont("helvetica", "normal");
    doc.text(facturaData.clienteNombre, 75, 62);

    doc.setFont("helvetica", "bold");
    doc.text("Identificación (RUC/C.I.):", 17, 69);
    doc.setFont("helvetica", "normal");
    doc.text(facturaData.clienteRuc, 60, 69);

    doc.setFont("helvetica", "bold");
    doc.text("Fecha Emisión:", 130, 69);
    doc.setFont("helvetica", "normal");
    doc.text(facturaData.fecha, 160, 69);

    doc.setFont("helvetica", "bold");
    doc.text("Dirección:", 17, 76);
    doc.setFont("helvetica", "normal");
    doc.text(facturaData.clienteDireccion.substring(0, 50), 38, 76);

    doc.setFont("helvetica", "bold");
    doc.text("Teléfono:", 130, 76);
    doc.setFont("helvetica", "normal");
    doc.text(facturaData.clienteTelefono || 'S/N', 150, 76);

    // Tabla de Detalles
    const tableColumn = ["Cod.", "Cantidad", "Descripción", "P. Unitario", "Descuento", "Total"];
    const tableRows = facturaData.items.map((item, index) => [
        `PRD-${index+1}`,
        item.cantidad,
        item.nombre,
        `$${Number(item.precioUnitario).toFixed(2)}`,
        `$0.00`, // Individual discounts not handled atm
        `$${(item.cantidad * item.precioUnitario).toFixed(2)}`
    ]);

    doc.autoTable({
        head: [tableColumn],
        body: tableRows,
        startY: 90,
        headStyles: { fillColor: [227, 0, 27], textColor: [255, 255, 255], fontStyle: 'bold' },
        styles: { fontSize: 9 },
    });

    const finalY = doc.lastAutoTable.finalY || 90;

    // Forma de Pago
    doc.roundedRect(14, finalY + 10, 80, 25, 2, 2, 'S');
    doc.setFont("helvetica", "bold");
    doc.text("Forma de Pago", 17, finalY + 16);
    doc.setFont("helvetica", "normal");
    doc.text(facturaData.metodoPago.toUpperCase().replace(/_/g, ' '), 17, finalY + 23);
    doc.text(`Valor: $${facturaData.total}`, 17, finalY + 30);

    // Subtotales (Derecha)
    const xTotals = 140;
    const xValues = 180;
    let currentY = finalY + 15;

    doc.setFont("helvetica", "bold");
    doc.text("SUBTOTAL 15%", xTotals, currentY);
    doc.setFont("helvetica", "normal");
    doc.text(`$${facturaData.subtotal}`, xValues, currentY);
    currentY += 6;

    doc.setFont("helvetica", "bold");
    doc.text("DESCUENTO", xTotals, currentY);
    doc.setFont("helvetica", "normal");
    doc.text(`$${facturaData.descuento}`, xValues, currentY);
    currentY += 6;

    doc.setFont("helvetica", "bold");
    doc.text("IVA 15%", xTotals, currentY);
    doc.setFont("helvetica", "normal");
    doc.text(`$${facturaData.iva}`, xValues, currentY);
    currentY += 6;

    doc.setFontSize(11);
    doc.setFont("helvetica", "bold");
    doc.text("VALOR TOTAL", xTotals, currentY);
    doc.text(`$${facturaData.total}`, xValues, currentY);

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
