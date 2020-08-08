// XXX: Keep in sync with @Route annotations in server-side controllers.
// We might want to auto-generate this someday...
export enum Route {
    AJAX_TRACKS_UPLOAD = '/ajax/tracks/upload',
    TRACKS_REVIEW = '/tracks/review',
    TRACKS_UPLOAD = '/tracks/upload',
    AJAX_TRACKS_ADD_TO_LIBRARY = '/ajax/tracks/addToLibrary',
    LIBRARY_TRACKS = '/',
    AJAX_TRACKS_STREAM = '/ajax/tracks/{id}/stream',
}

type RouteParameterValue = string | string[] | number | number[] | boolean | boolean[];
type RouteParameterSet = {
    [key: string]: RouteParameterValue;
};

export function generateUrl(route: Route, parameters: RouteParameterSet = {}) {
    const parameterIsPartOfPath: { [key: string]: boolean } = {};
    const queryStringComponents: [string, string][] = [];

    function encodePathComponent(value: RouteParameterValue): string {
        return encodeURIComponent(String(value));
    }

    function addQueryComponentsForParameter(parameter: string, value: RouteParameterValue): void {
        if (!Array.isArray(value)) {
            queryStringComponents.push([parameter, String(value)]);

            return;
        }

        for (const item of value) {
            queryStringComponents.push([`${parameter}[]`, String(item)]);
        }
    }

    function replaceRoutePlaceholder(match: string, parameter: string): string {
        parameterIsPartOfPath[parameter] = true;

        return encodePathComponent(parameters[parameter]);
    }

    // NOTE: this does not implement the full Symfony route placeholder syntax.
    let url = route.replace(/[{]([a-zA-Z0-9_]+)[}]/g, replaceRoutePlaceholder);
    let hasQueryString = false;

    for (const parameter of Object.keys(parameters)) {
        if (!parameterIsPartOfPath.hasOwnProperty(parameter)) {
            addQueryComponentsForParameter(parameter, parameters[parameter]);
            hasQueryString = true;
        }
    }

    if (hasQueryString) {
        url += '?' + queryStringComponents
            .map(([key, value]) => encodeURIComponent(key) + '=' + encodeURIComponent(value))
            .join('&');
    }

    return url;
}
