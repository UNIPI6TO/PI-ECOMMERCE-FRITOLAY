export const CarritoManager = {
    cookieName: 'fritolay_cart',

    _getCookie() {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${this.cookieName}=`);
        if (parts.length === 2) {
            try {
                return JSON.parse(decodeURIComponent(parts.pop().split(';').shift()));
            } catch (e) {
                return [];
            }
        }
        return [];
    },

    _setCookie(cart) {
        const d = new Date();
        d.setTime(d.getTime() + (24 * 60 * 60 * 1000)); // 24 hours
        let expires = "expires=" + d.toUTCString();
        document.cookie = `${this.cookieName}=${encodeURIComponent(JSON.stringify(cart))};${expires};path=/;SameSite=Strict`;
        window.dispatchEvent(new Event('cart-updated'));
    },

    agregarItemConValidacion(productoId, nombre, cantidad, precioUnitario, stockDisponible, unidadesPorPaca = 1, imagen = '') {
        let cart = this._getCookie();
        let existing = cart.find(item => item.productoId === productoId);
        let cantidadActualEnCarrito = existing ? existing.cantidad : 0;
        let cantidadDeseada = parseInt(cantidad, 10);
        let totalPropuesto = cantidadActualEnCarrito + cantidadDeseada;

        if (totalPropuesto > stockDisponible) {
            let maximoAdicional = stockDisponible - cantidadActualEnCarrito;
            if (maximoAdicional <= 0) {
                return { exito: false, mensaje: `Ya tienes todas las ${stockDisponible} unidades disponibles de "${nombre}" en tu carrito.` };
            }
            return { exito: false, mensaje: `Solo quedan ${maximoAdicional} unidades disponibles adicionales para agregar a tu carrito.` };
        }

        if (existing) {
            existing.cantidad += cantidadDeseada;
            if(unidadesPorPaca) existing.unidadesPorPaca = unidadesPorPaca;
            if(imagen) existing.imagen = imagen;
        } else {
            cart.push({
                productoId,
                nombre,
                cantidad: cantidadDeseada,
                precioUnitario: parseFloat(precioUnitario),
                unidadesPorPaca: parseInt(unidadesPorPaca, 10) || 1,
                imagen: imagen || ''
            });
        }
        this._setCookie(cart);
        return { exito: true };
    },

    agregarItem(productoId, nombre, cantidad, precioUnitario, unidadesPorPaca = 1, imagen = '') {
        let cart = this._getCookie();
        let existing = cart.find(item => item.productoId === productoId);
        
        if (existing) {
            existing.cantidad += parseInt(cantidad, 10);
            if(unidadesPorPaca) existing.unidadesPorPaca = unidadesPorPaca;
            if(imagen) existing.imagen = imagen;
        } else {
            cart.push({
                productoId,
                nombre,
                cantidad: parseInt(cantidad, 10),
                precioUnitario: parseFloat(precioUnitario),
                unidadesPorPaca: parseInt(unidadesPorPaca, 10) || 1,
                imagen: imagen || ''
            });
        }
        this._setCookie(cart);
    },

    actualizarCantidad(productoId, nuevaCantidad) {
        let cart = this._getCookie();
        let item = cart.find(i => i.productoId === productoId);
        if (item) {
            let qty = parseInt(nuevaCantidad, 10);
            if (qty <= 0) {
                this.eliminarItem(productoId);
                return;
            }
            item.cantidad = qty;
            this._setCookie(cart);
        }
    },

    incrementarCantidad(productoId, paso = 1) {
        let cart = this._getCookie();
        let item = cart.find(i => i.productoId === productoId);
        if (item) {
            item.cantidad += parseInt(paso, 10);
            this._setCookie(cart);
        }
    },

    decrementarCantidad(productoId, paso = 1) {
        let cart = this._getCookie();
        let item = cart.find(i => i.productoId === productoId);
        if (item) {
            let nueva = item.cantidad - parseInt(paso, 10);
            if (nueva <= 0) {
                this.eliminarItem(productoId);
            } else {
                item.cantidad = nueva;
                this._setCookie(cart);
            }
        }
    },

    mergeItem(productoId, cantidad) {
        let cart = this._getCookie();
        let existing = cart.find(item => item.productoId === productoId);
        if (existing) {
            existing.cantidad += parseInt(cantidad, 10);
            this._setCookie(cart);
        }
    },

    eliminarItem(productoId) {
        let cart = this._getCookie();
        cart = cart.filter(item => item.productoId !== productoId);
        this._setCookie(cart);
    },

    calcularSubtotal() {
        let cart = this._getCookie();
        return cart.reduce((total, item) => total + (item.cantidad * item.precioUnitario), 0);
    },

    getItems() {
        return this._getCookie();
    },

    vaciar() {
        this._setCookie([]);
    },

    /** Número de ítems únicos (no suma de unidades) — para el badge del carrito */
    getCount() {
        return this._getCookie().length;
    },

    /** Registra el abandono en el backend y luego vacía el carrito */
    async abandonarCarrito(motivo = 'Carrito vaciado manualmente') {
        const cart = this._getCookie();
        if (cart.length === 0) {
            this.vaciar();
            return;
        }
        const valorTotal = parseFloat(this.calcularSubtotal());
        let clienteId = null;
        try {
            const token = localStorage.getItem('jwt_token');
            if (token) {
                const payload = JSON.parse(atob(token.split('.')[1]));
                clienteId = payload.cliente_id || payload.sub || null;
            }
        } catch (_) {}

        const backendUrl = (typeof window !== 'undefined' && window.BACKEND_URL) ? window.BACKEND_URL : 'http://localhost:8000';

        try {
            if (typeof window !== 'undefined' && window.api) {
                await window.api('/api/carritos-abandonados', {
                    method: 'POST',
                    body: JSON.stringify({
                        cliente_id: clienteId,
                        motivo_cancelacion: motivo,
                        valor_total: valorTotal
                    })
                });
            } else {
                await fetch(`${backendUrl}/api/carritos-abandonados`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        cliente_id: clienteId,
                        motivo_cancelacion: motivo,
                        valor_total: valorTotal
                    })
                });
            }
        } catch (e) {
            console.warn('[CarritoManager] No se pudo registrar abandono:', e);
        } finally {
            this.vaciar();
        }
    }
};
