import { jsPDF } from "jspdf";
import "jspdf-autotable";

/** Cache de la configuración de empresa para no llamar la API en cada factura */
let _empresaCache = null;

async function getEmpresaConfig() {
    if (_empresaCache) return _empresaCache;
    try {
        const res = await fetch(`${window.BACKEND_URL}/api/empresa`, {
            headers: { 'Accept': 'application/json' }
        });
        if (res.ok) {
            const json = await res.json();
            _empresaCache = json.data;
        }
    } catch (e) {
        console.warn('[pdf-generator] No se pudo cargar empresa_config, usando valores por defecto.', e);
    }
    // Fallback a datos hardcoded si la API falla
    if (!_empresaCache) {
        _empresaCache = {
            razon_social: 'Pepsico Alimentos Ecuador Cia. Ltda.',
            nombre_comercial: 'Fritolay Ambato',
            ruc: '1790205401001',
            codigo_establecimiento: '003',
            punto_emision: '001',
            direccion_matriz: 'Av. General Rumiñahui Lote 2, Sangolquí, Pichincha',
            direccion_sucursal: 'Zona Industrial de Ambato, Tungurahua, Ecuador',
            telefono: '032-000-000',
            tipo_contribuyente: 'ESPECIAL',
            obligado_contabilidad: true,
            color_primario: '#E3001B',
        };
    }
    return _empresaCache;
}

/**
 * Genera el número de factura formateado según SRI Ecuador.
 * Formato: {est}-{pto}-{secuencial}
 */
function formatNumeroSRI(empresa, secuencial) {
    const est = (empresa.codigo_establecimiento || '003').padStart(3, '0');
    const pto = (empresa.punto_emision || '001').padStart(3, '0');
    const seq = String(secuencial).padStart(9, '0');
    return `${est}-${pto}-${seq}`;
}

/**
 * Convierte color hex #RRGGBB a array [R, G, B]
 */
function hexToRgb(hex) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return [r, g, b];
}

export const generateFactura = async (facturaData) => {
    const empresa = await getEmpresaConfig();
    const [r, g, b] = hexToRgb(empresa.color_primario || '#E3001B');
    const doc = new jsPDF();

    // ── Header Banner ─────────────────────────────────────────────────────────
    doc.setFillColor(r, g, b);
    doc.rect(0, 0, 210, 38, 'F');

    doc.setTextColor(255, 255, 255);
    doc.setFontSize(18);
    doc.setFont("helvetica", "bold");
    doc.text(empresa.nombre_comercial || empresa.razon_social, 14, 18);
    doc.setFontSize(9);
    doc.setFont("helvetica", "normal");
    doc.text(empresa.razon_social, 14, 25);
    doc.text(`RUC: ${empresa.ruc}`, 14, 31);
    doc.text(`Contribuyente ${empresa.tipo_contribuyente}`, 14, 36);

    // ── Cuadro SRI (derecha) ──────────────────────────────────────────────────
    doc.setFillColor(255, 255, 255);
    doc.roundedRect(118, 5, 87, 30, 2, 2, 'F');
    doc.setDrawColor(200, 200, 200);
    doc.roundedRect(118, 5, 87, 30, 2, 2, 'S');

    doc.setTextColor(0, 0, 0);
    doc.setFontSize(9);
    doc.setFont("helvetica", "normal");
    const numSRI = facturaData.numero;
    doc.setFont("helvetica", "bold");
    doc.text("FACTURA", 120, 12);
    doc.setFont("helvetica", "normal");
    doc.text(`No. ${numSRI}`, 120, 18);
    doc.text(`Ambiente: ${empresa.tipo_ambiente === '1' ? 'PRUEBAS' : 'PRODUCCIÓN'}`, 120, 24);
    doc.text(`Emisión: ${empresa.tipo_emision === '1' ? 'NORMAL' : 'IND. ELEC.'}`, 120, 30);

    // ── Datos Empresa ─────────────────────────────────────────────────────────
    doc.setFontSize(8);
    doc.setTextColor(60, 60, 60);
    doc.text(`Dir. Matriz: ${empresa.direccion_matriz}`, 14, 44);
    if (empresa.direccion_sucursal) {
        doc.text(`Dir. Sucursal: ${empresa.direccion_sucursal}`, 14, 49);
    }
    doc.text(`Obligado a llevar contabilidad: ${empresa.obligado_contabilidad ? 'SÍ' : 'NO'}`, 14, 54);

    // ── Datos del Cliente ─────────────────────────────────────────────────────
    doc.setDrawColor(180, 180, 180);
    doc.roundedRect(14, 58, 183, 28, 2, 2, 'S');
    doc.setFontSize(8.5);

    doc.setFont("helvetica", "bold"); doc.setTextColor(0, 0, 0);
    doc.text("Razón Social / Nombres:", 17, 65);
    doc.setFont("helvetica", "normal");
    doc.text(String(facturaData.clienteNombre || 'Consumidor Final').substring(0, 55), 68, 65);

    doc.setFont("helvetica", "bold");
    doc.text("Identificación (RUC/C.I.):", 17, 71);
    doc.setFont("helvetica", "normal");
    doc.text(String(facturaData.clienteRuc || '9999999999999'), 65, 71);

    doc.setFont("helvetica", "bold");
    doc.text("Fecha Emisión:", 130, 71);
    doc.setFont("helvetica", "normal");
    doc.text(facturaData.fecha, 162, 71);

    doc.setFont("helvetica", "bold");
    doc.text("Dirección:", 17, 77);
    doc.setFont("helvetica", "normal");
    doc.text(String(facturaData.clienteDireccion || 'S/N').substring(0, 50), 37, 77);

    doc.setFont("helvetica", "bold");
    doc.text("Teléfono:", 130, 77);
    doc.setFont("helvetica", "normal");
    doc.text(String(facturaData.clienteTelefono || 'S/N'), 152, 77);

    // ── Tabla Detalles ────────────────────────────────────────────────────────
    const tableColumn = ["#", "Cantidad", "Descripción", "P. Unit.", "Descuento", "Total"];
    const tableRows = (facturaData.items || []).map((item, index) => [
        `${index + 1}`,
        item.cantidad,
        item.nombre,
        `$${Number(item.precioUnitario).toFixed(2)}`,
        `$0.00`,
        `$${(item.cantidad * item.precioUnitario).toFixed(2)}`
    ]);

    doc.autoTable({
        head: [tableColumn],
        body: tableRows,
        startY: 90,
        headStyles: { fillColor: [r, g, b], textColor: [255, 255, 255], fontStyle: 'bold', fontSize: 8 },
        styles: { fontSize: 8 },
        columnStyles: { 2: { cellWidth: 60 } }
    });

    const finalY = doc.lastAutoTable.finalY || 90;

    // ── Forma de Pago ─────────────────────────────────────────────────────────
    doc.roundedRect(14, finalY + 8, 80, 22, 2, 2, 'S');
    doc.setFont("helvetica", "bold"); doc.setFontSize(8.5);
    doc.text("Forma de Pago", 17, finalY + 14);
    doc.setFont("helvetica", "normal");
    doc.text(String(facturaData.metodoPago || '').toUpperCase().replace(/_/g, ' '), 17, finalY + 20);
    doc.text(`Valor: $${Number(facturaData.total).toFixed(2)}`, 17, finalY + 26);

    // ── Totales ───────────────────────────────────────────────────────────────
    const xL = 130; const xR = 195;
    let cy = finalY + 14;
    const row = (label, value) => {
        doc.setFont("helvetica", "bold"); doc.text(label, xL, cy);
        doc.setFont("helvetica", "normal"); doc.text(`$${Number(value).toFixed(2)}`, xR, cy, { align: 'right' });
        cy += 6;
    };
    row("SUBTOTAL:", facturaData.subtotal);
    row("DESCUENTO:", facturaData.descuento || 0);
    row("IVA 15%:", facturaData.iva);

    doc.setFontSize(10); doc.setFont("helvetica", "bold");
    doc.text("VALOR TOTAL:", xL, cy);
    doc.text(`$${Number(facturaData.total).toFixed(2)}`, xR, cy, { align: 'right' });

    // ── Footer ────────────────────────────────────────────────────────────────
    doc.setFontSize(7); doc.setFont("helvetica", "italic"); doc.setTextColor(120, 120, 120);
    doc.text(`Documento generado el ${new Date().toLocaleString('es-EC')} — Ambiente: ${empresa.tipo_ambiente === '1' ? 'PRUEBAS' : 'PRODUCCIÓN'}`, 14, 285);

    doc.save(`factura_${numSRI}.pdf`);
};

export const generateNotaCredito = async (notaData) => {
    const empresa = await getEmpresaConfig();
    const [r, g, b] = hexToRgb(empresa.color_primario || '#E3001B');
    const doc = new jsPDF();

    // ── Header Banner ─────────────────────────────────────────────────────────
    doc.setFillColor(r, g, b);
    doc.rect(0, 0, 210, 38, 'F');

    doc.setTextColor(255, 255, 255);
    doc.setFontSize(18);
    doc.setFont("helvetica", "bold");
    doc.text(empresa.nombre_comercial || empresa.razon_social, 14, 18);
    doc.setFontSize(9);
    doc.setFont("helvetica", "normal");
    doc.text(empresa.razon_social, 14, 25);
    doc.text(`RUC: ${empresa.ruc}`, 14, 31);
    doc.text(`Contribuyente ${empresa.tipo_contribuyente}`, 14, 36);

    // ── Cuadro SRI (derecha) ──────────────────────────────────────────────────
    doc.setFillColor(255, 255, 255);
    doc.roundedRect(118, 5, 87, 30, 2, 2, 'F');
    doc.setDrawColor(200, 200, 200);
    doc.roundedRect(118, 5, 87, 30, 2, 2, 'S');

    doc.setTextColor(0, 0, 0);
    doc.setFontSize(9);
    const numSRI = notaData.numeroNota || `NC-${notaData.id || '001'}`;
    doc.setFont("helvetica", "bold");
    doc.text("NOTA DE CRÉDITO", 120, 12);
    doc.setFont("helvetica", "normal");
    doc.text(`No. ${numSRI}`, 120, 18);
    doc.text(`Ambiente: ${empresa.tipo_ambiente === '1' ? 'PRUEBAS' : 'PRODUCCIÓN'}`, 120, 24);
    doc.text(`Emisión: ${empresa.tipo_emision === '1' ? 'NORMAL' : 'IND. ELEC.'}`, 120, 30);

    // ── Datos Empresa ─────────────────────────────────────────────────────────
    doc.setFontSize(8);
    doc.setTextColor(60, 60, 60);
    doc.text(`Dir. Matriz: ${empresa.direccion_matriz}`, 14, 44);
    if (empresa.direccion_sucursal) {
        doc.text(`Dir. Sucursal: ${empresa.direccion_sucursal}`, 14, 49);
    }
    doc.text(`Obligado a llevar contabilidad: ${empresa.obligado_contabilidad ? 'SÍ' : 'NO'}`, 14, 54);

    // ── Datos del Cliente & Comprobante Modificado ─────────────────────────────
    doc.setDrawColor(180, 180, 180);
    doc.roundedRect(14, 58, 183, 34, 2, 2, 'S');
    doc.setFontSize(8.5);

    doc.setFont("helvetica", "bold"); doc.setTextColor(0, 0, 0);
    doc.text("Razón Social / Nombres:", 17, 64);
    doc.setFont("helvetica", "normal");
    doc.text(String(notaData.clienteNombre || 'Consumidor Final').substring(0, 55), 68, 64);

    doc.setFont("helvetica", "bold");
    doc.text("Identificación (RUC/C.I.):", 17, 70);
    doc.setFont("helvetica", "normal");
    doc.text(String(notaData.clienteRuc || '9999999999999'), 65, 70);

    doc.setFont("helvetica", "bold");
    doc.text("Fecha Emisión:", 130, 70);
    doc.setFont("helvetica", "normal");
    doc.text(notaData.fecha || new Date().toLocaleDateString('es-EC'), 162, 70);

    doc.setFont("helvetica", "bold");
    doc.text("Comprobante Modificado:", 17, 76);
    doc.setFont("helvetica", "normal");
    doc.text(`FACTURA ${notaData.facturaNumero || ''}`, 68, 76);

    doc.setFont("helvetica", "bold");
    doc.text("Motivo de Modificación:", 17, 82);
    doc.setFont("helvetica", "normal");
    doc.text(String(notaData.motivo || 'Devolución Total').substring(0, 60), 68, 82);

    // ── Tabla Detalles ────────────────────────────────────────────────────────
    const tableColumn = ["#", "Cantidad", "Descripción", "P. Unit.", "Descuento", "Total"];
    const tableRows = (notaData.items || []).map((item, index) => [
        `${index + 1}`,
        item.cantidad,
        item.nombre,
        `$${Number(item.precioUnitario).toFixed(2)}`,
        `$0.00`,
        `$${(item.cantidad * item.precioUnitario).toFixed(2)}`
    ]);

    doc.autoTable({
        head: [tableColumn],
        body: tableRows,
        startY: 96,
        headStyles: { fillColor: [r, g, b], textColor: [255, 255, 255], fontStyle: 'bold', fontSize: 8 },
        styles: { fontSize: 8 },
        columnStyles: { 2: { cellWidth: 60 } }
    });

    const finalY = doc.lastAutoTable.finalY || 96;

    // ── Totales ───────────────────────────────────────────────────────────────
    const xL = 130; const xR = 195;
    let cy = finalY + 14;
    const row = (label, value) => {
        doc.setFont("helvetica", "bold"); doc.text(label, xL, cy);
        doc.setFont("helvetica", "normal"); doc.text(`$${Number(value).toFixed(2)}`, xR, cy, { align: 'right' });
        cy += 6;
    };
    row("SUBTOTAL MODIFICADO:", notaData.subtotal);
    row("DESCUENTO:", notaData.descuento || 0);
    row("IVA 15%:", notaData.iva);

    doc.setFontSize(10); doc.setFont("helvetica", "bold");
    doc.text("VALOR MODIFICADO:", xL, cy);
    doc.text(`$${Number(notaData.total).toFixed(2)}`, xR, cy, { align: 'right' });

    // ── Footer ────────────────────────────────────────────────────────────────
    doc.setFontSize(7); doc.setFont("helvetica", "italic"); doc.setTextColor(120, 120, 120);
    doc.text(`Documento generado el ${new Date().toLocaleString('es-EC')} — Ambiente: ${empresa.tipo_ambiente === '1' ? 'PRUEBAS' : 'PRODUCCIÓN'}`, 14, 285);

    doc.save(`nota_credito_${numSRI}.pdf`);
};

export const generateGuiaRemision = async (guiaData) => {
    const empresa = await getEmpresaConfig();
    const [r, g, b] = hexToRgb(empresa.color_primario || '#E3001B');
    const doc = new jsPDF();

    doc.setFillColor(r, g, b);
    doc.rect(0, 0, 210, 28, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(16); doc.setFont("helvetica", "bold");
    doc.text("GUÍA DE REMISIÓN", 14, 14);
    doc.setFontSize(9); doc.setFont("helvetica", "normal");
    doc.text(`${empresa.nombre_comercial} | RUC: ${empresa.ruc}`, 14, 22);

    doc.setTextColor(0, 0, 0); doc.setFontSize(9);
    doc.text(`No. Guía: ${guiaData.id || 'N/A'}`, 14, 35);
    doc.text(`Camión: ${guiaData.camion || 'N/A'}`, 14, 41);
    doc.text(`Fecha: ${new Date().toLocaleDateString('es-EC')}`, 14, 47);

    doc.save(`guia_remision_${guiaData.id || 'N'}.pdf`);
};

export const generateGuiaRuta = async (guiaData) => {
    const empresa = await getEmpresaConfig();
    const [r, g, b] = hexToRgb(empresa.color_primario || '#E3001B');
    const doc = new jsPDF();

    doc.setFillColor(r, g, b);
    doc.rect(0, 0, 210, 28, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(16); doc.setFont("helvetica", "bold");
    doc.text("GUÍA DE RUTA", 14, 14);
    doc.setFontSize(9); doc.setFont("helvetica", "normal");
    doc.text(`${empresa.nombre_comercial} | RUC: ${empresa.ruc}`, 14, 22);

    doc.setTextColor(0, 0, 0); doc.setFontSize(9);
    doc.text(`No. Ruta: ${guiaData.id || 'N/A'}`, 14, 35);
    doc.text(`Fecha: ${new Date().toLocaleDateString('es-EC')}`, 14, 41);

    doc.save(`guia_ruta_${guiaData.id || 'N'}.pdf`);
};
