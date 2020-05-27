export function generateUniqueID() {
    const values = new Uint8Array(16);
    crypto.getRandomValues(values);

    return Array.from(values)
        .map((byte) => byte.toString(16).padStart(2, '0'))
        .join('');
}
