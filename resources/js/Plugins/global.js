export default {
    install: (app) => {
        app.config.globalProperties.$filters = {
            currency(value, setAsNumber = false) {
                if (typeof value !== "number") {
                    return value;
                }
                var formatter = new Intl.NumberFormat("en-US", {
                    style: setAsNumber ? undefined : "currency",
                    currency: setAsNumber ? undefined : "USD",
                    minimumFractionDigits: 0,
                });
                return formatter.format(value);
            },
            format(value, type) {
                //if (!!!value) return value;
                //console.log(value, type);
                if (!!value && !!value.format) (type = value.format), (value = value.value);
                if (!!!type) return value;
                if (type === "text") return value;
                if (type === "percentage") return !!value ? parseFloat(value).toFixed(2) + "%" : null;
                if (type === "number") {
                    return !!value ? this.currency(parseFloat(value), true) : value;
                }
                if (type === "currency") return !!value ? this.currency(parseFloat(value)) : value;
            },
            formatByKey(key, value) {
                const formats = {
                    total: (val) => this.format(val, "currency"),
                    Total: (val) => this.format(val, "currency"),
                    unit_price: (val) => this.format(val, "currency"),
                    suma: (val) => this.format(val, "currency"),
                    amount: (val) => this.format(val, "currency"),
                    invoiced: (val) => this.format(val, "currency"),
                    to_invoice: (val) => this.format(val, "currency"),
                    paid: (val) => this.format(val, "currency"),
                    to_pay: (val) => this.format(val, "currency"),
                    quantity: (val) => this.format(val, "number"),
                    total_quantity: (val) => this.format(val, "number"),
                    total_estimate_per: (val) => this.format(val, "percentage"),
                    total_estimate: (val) => this.format(val, "number"),
                    estimated: (val) => this.format(val, "number"),
                    to_estimate: (val) => this.format(val, "number"),
                    importe: (val) => this.format(val, "currency"),
                    Importe: (val) => this.format(val, "currency"),
                    cargo: (val) => this.format(val, "currency"),
                    abono: (val) => this.format(val, "currency"),
                    saldo: (val) => this.format(val, "currency"),
                    invoice_amount: (val) => this.format(val.amount, "currency"),
                    total_amount: (val) => this.format(val.amount, "currency"),
                    still: (val) => this.format(val.amount, "currency"),
                    still_to_invoce: (val) => this.format(val.amount, "currency"),
                    //date: (val) => Date.parse(val).toLocaleString()//this.format(val, 'currency'),
                };
                return formats[key] ? formats[key](value) : value;
            },
            fillObject(props, fill) {
                return props.reduce((pv, cv) => {
                    pv[cv] = fill;
                    return pv;
                }, {});
            },
        };
        app.config.globalProperties.$s = {
            f(v) {
                return isNaN(v) ? 0 : v === "" ? 0 : parseFloat(v ?? 0);
            },
            getTags(val, fn='match') {
                if (!val) return [];
                const sym = "(\\#|\\$|\\*|\\@|\\!|\\?|\\+)";
                var match = new RegExp(`(${sym}[a-zA-Z0-9(_)]{1,})`, "gm");
                match = val[fn](match);
                return !!match ? match : [];
            },
        };
    },
};
