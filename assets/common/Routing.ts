// XXX: Keep in sync with @Route annotations in server-side controllers.
// We might want to auto-generate this someday...
export enum Route {
    AJAX_TRACKS_UPLOAD = '/ajax/tracks/upload',
    TRACKS_REVIEW = '/tracks/review',
    AJAX_TRACKS_ADD_TO_LIBRARY = '/ajax/tracks/addToLibrary',
}

type RouteParameters = {
    [key: string]: string | number | boolean;
};

export function generateUrl(route: Route, parameters: RouteParameters = {}) {
    const parameterOccursInRoute: { [key: string]: boolean } = {};

    function encodeParameterValue(key: string): string {
        const value = parameters[key];

        return encodeURIComponent(String(value));
    }

    function replaceRoutePlaceholder(_match: string, parameterKey: string): string {
        parameterOccursInRoute[parameterKey] = true;

        return encodeParameterValue(parameterKey);
    }

    let url = route.replace(/[{]([a-zA-Z0-9_]+)[}]/g, replaceRoutePlaceholder);
    let hasQueryString = false;
    const queryStringComponents: { [key: string]: string } = {};

    for (const parameterKey of Object.keys(parameters)) {
        if (!parameterOccursInRoute.hasOwnProperty(parameterKey)) {
            queryStringComponents[parameterKey] = encodeParameterValue(parameterKey);
            hasQueryString = true;
        }
    }

    if (hasQueryString) {
        url += '?' + Object.entries(queryStringComponents)
            .map(([k, v]) => encodeURIComponent(k) + '=' + v)
            .join('&');
    }

    return url;
}
