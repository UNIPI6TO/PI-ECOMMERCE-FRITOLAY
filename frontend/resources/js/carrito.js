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

    agregarItem(productoId, nombre, cantidad, precioUnitario) {
        let cart = this._getCookie();
        let existing = cart.find(item => item.productoId === productoId);
        
        if (existing) {
            existing.cantidad += parseInt(cantidad, 10);
        } else {
            cart.push({
                productoId,
                nombre,
                cantidad: parseInt(cantidad, 10),
                precioUnitario: parseFloat(precioUnitario)
            });
        }
        this._setCookie(cart);
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
        return cart.reduce((total, item) => total + (item.cantidad * item.precioUnitario), 0).toFixed(2);
    },

    getItems() {
        return this._getCookie();
    },

    vaciar() {
        this._setCookie([]);
    },

    getCount() {
        let cart = this._getCookie();
        return cart.reduce((total, item) => total + item.cantidad, 0);
    }
};
