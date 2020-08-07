import React, { ReactNode } from 'react';
import { UploadedFile, UploadedFileStatus } from './UploadTypes';
import toReadableSize from './toReadableSize';
import Icon, { Icons } from '../common/Icons';

type UploadQueueProps = { queue: UploadedFile[] };

const UploadQueue: React.FC<UploadQueueProps> = ({ queue }) => {
    function buildStatusIcon(status: UploadedFileStatus): ReactNode {
        switch (status) {
            case UploadedFileStatus.PENDING:
                return (
                    <span className="upload-queue-status-icon upload-queue-status-icon-pending">
                        <Icon icon={Icons.PENDING}/>
                    </span>
                );

            case UploadedFileStatus.STARTED:
                return (
                    <span className="upload-queue-status-icon upload-queue-status-icon-started">
                        <Icon icon={Icons.SAVING}/>
                    </span>
                );

            case UploadedFileStatus.SUCCESS:
                return (
                    <span className="upload-queue-status-icon upload-queue-status-icon-success">
                        <Icon icon={Icons.SUCCESS}/>
                    </span>
                );

            case UploadedFileStatus.ERROR:
                return (
                    <span className="upload-queue-status-icon upload-queue-status-icon-error">
                        <Icon icon={Icons.ERROR}/>
                    </span>
                );
        }
    }

    return (
        <div className="upload-queue">
            <div className="upload-queue-heading">Upload queue</div>
            {queue.length === 0 && (
                <div className="upload-queue-empty">
                    Uploaded files will appear here…
                </div>
            )}
            <div className="upload-queue-items">
                {queue.map((file) => (
                    <div className="upload-queue-item" key={file.id}>
                        {file.status === UploadedFileStatus.STARTED && (
                            <div className="upload-queue-item-meter" style={{ width: `${file.percentage}%` }}/>
                        )}
                        <div className="upload-queue-item-status">
                            {buildStatusIcon(file.status)}
                        </div>
                        <div className="upload-queue-item-info">
                            <span className="upload-queue-item-name">
                                {file.name}
                            </span>
                            <span className="upload-queue-item-detail">
                                {file.status === UploadedFileStatus.STARTED && (
                                    `${toReadableSize(file.bytesUploaded)} / `
                                )}
                                {toReadableSize(file.sizeBytes)}
                            </span>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default UploadQueue;
