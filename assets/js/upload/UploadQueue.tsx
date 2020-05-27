import React, { ReactNode } from 'react';
import { UploadedFile, UploadedFileStatus } from './UploadTypes';
import toReadableSize from './toReadableSize';
import { Check, Clock, UploadCloud, X } from 'react-feather';

type UploadQueueProps = { queue: UploadedFile[] };

const UploadQueue: React.FC<UploadQueueProps> = ({ queue }) => {
    if (queue.length === 0) {
        return <></>;
    }

    function buildStatusIcon(status: UploadedFileStatus): ReactNode {
        switch (status) {
            case UploadedFileStatus.SUCCESS:
                return (
                    <span className="upload-queue-status-icon upload-queue-status-icon-success">
                        <Check color="mediumseagreen"/>
                    </span>
                );

            case UploadedFileStatus.ERROR:
                return (
                    <span className="upload-queue-status-icon upload-queue-status-icon-error">
                        <X color="orangered"/>
                    </span>
                );

            case UploadedFileStatus.PENDING:
                return (
                    <span className="upload-queue-status-icon upload-queue-status-icon-pending">
                        <Clock color="darkgray"/>
                    </span>
                );

            case UploadedFileStatus.STARTED:
                return (
                    <span className="upload-queue-status-icon upload-queue-status-icon-started">
                        <UploadCloud color="cornflowerblue"/>
                    </span>
                );
        }
    }

    return (
        <div className="upload-queue">
            <div className="upload-queue-heading">Upload queue</div>
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
