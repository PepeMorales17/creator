export function setupMenu(theMenu, routes, key = 'sec') {

    const menu = JSON.parse(JSON.stringify(theMenu));
    //console.log(routes, menu, theMenu);
    routes.map(x => {
        menu[key][x.name].routeWith = route(menu[key][x.name].route, x.data);
    })
    return menu;
}


export function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}


export function insertParam(key, value) {
    key = encodeURIComponent(key);
    value = encodeURIComponent(value);

    // kvp looks like ['key1=value1', 'key2=value2', ...]
    var kvp = document.location.search.substr(1).split('&');
    let i=0;

    for(; i<kvp.length; i++){
        if (kvp[i].startsWith(key + '=')) {
            let pair = kvp[i].split('=');
            pair[1] = value;
            kvp[i] = pair.join('=');
            break;
        }
    }

    if(i >= kvp.length){
        kvp[kvp.length] = [key,value].join('=');
    }

    // can return this or...
    let params = kvp.join('&');

    // reload page with new params
    document.location.search = params;
}

export const SetUrlParams = {
    data() {
        return {
            myInterceptor: null,
        };
    },
    mounted() {
        this.myInterceptor = axios.interceptors.request.use(
            function (config) {
                // Do something before request is sentasd
                if (config.url.search(route().current().split('.')[0]) > -1) {
                    config.params = Object.fromEntries(new URLSearchParams(window.location.search).entries());
                }

                return config;
            },
            function (error) {
                // Do something with request error
                return Promise.reject(error);
            }
        );
    },

    unmounted() {
        axios.interceptors.request.eject(this.myInterceptor);
    },
}
