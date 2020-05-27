export default function toReadableSize(bytes: number): string {
    const precision = 2;

    if (bytes > 1024 * 1024) {
        return (bytes / 1024 / 1024).toFixed(precision) + ' MiB';
    } else if (bytes > 1024) {
        return (bytes / 1024).toFixed(precision) + ' KiB';
    }

    return bytes + ' B';
}
