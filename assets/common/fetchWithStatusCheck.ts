export class HttpResponseError extends Error {
    constructor(message?: string) {
        super(message);
    }
}

export default function fetchWithStatusCheck(input: RequestInfo, init?: RequestInit): Promise<Response> {
    return fetch(input, init)
        .then((response) => {
            if (response.status >= 400) {
                throw new HttpResponseError(`Response finished with error: ${response.status}`);
            }

            return response;
        });
}
