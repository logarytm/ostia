import '../css/tagging.css';
import React, { ReactNode } from 'react';
import { render } from 'react-dom';
import { ArrowRight, Check, Clock, UploadCloud, X } from 'react-feather';
import { Emitter } from 'event-kit';
import $ from 'jquery';

enum TrackReviewStatus {
    PENDING = 'pending',
    SAVING = 'saving',
    SAVED = 'saved',
    ERROR = 'error',
}

type TrackToReview = {
    readonly name: string;
    readonly uuid: string;
    title: string | null;
    artists: string[] | null;
    albumArtists: string[] | null;
    album: string | null;
    trackNo: number | null;
    status: TrackReviewStatus;
};

declare var __trackFiles: TrackToReview[];

type TaggingViewProps = {
    files: TrackToReview[];
};

type TaggingViewState = {
    files: TrackToReview[];
};

function addFilesToLibrary(files: TrackToReview[], emitter: SavingEmitter) {
    const file = files.find((file) => file.status === TrackReviewStatus.PENDING);
    if (!file) {
        return;
    }

    file.status = TrackReviewStatus.SAVING;
    emitter.emit('progress', { file });

    $.ajax({
        url: '/tracks/ajaxAddToLibrary',
        type: 'post',
        dataType: 'json',
        data: { uuid: file.uuid },
        complete: () => {
            addFilesToLibrary(files, emitter);
        },
        success: () => {
            file.status = TrackReviewStatus.SAVED;
            emitter.emit('progress', { file });
        },
        error: (error) => {
            console.log(error);
            file.status = TrackReviewStatus.ERROR;
            emitter.emit('progress', { file });
        },
    });
}

type SavingEmissions = {
    progress: { file: TrackToReview };
};

type SavingEmitter = Emitter<SavingEmissions, SavingEmissions>;

class TaggingView extends React.Component<TaggingViewProps, TaggingViewState> {
    private readonly emitter: SavingEmitter;

    public constructor(props: TaggingViewProps) {
        super(props);

        this.state = { files: props.files };
        this.emitter = new Emitter<SavingEmissions, SavingEmissions>();
        this.emitter.on('progress', ({ file }) => {
            this.forceUpdate();
        });
    }

    public render(): ReactNode {
        return (
            <div className="saving-view">
                <div className="saving-view-items">
                    {this.state.files.map((file) => (
                        <div className="saving-item" key={file.uuid}>
                            <div className="saving-item-status">
                                {this.renderStatusIcon(file.status)}
                            </div>
                            <div className="saving-item-info">
                                <div className="saving-item-name">
                                    {file.name}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
                {this.state.files.length === 0
                    ? (
                        <div className="help">
                            All files have been added to your library.
                            {this.renderProceed()}
                        </div>
                    )
                    : this.renderProceed()
                }
            </div>
        );
    }

    public handleProceed(): void {
        addFilesToLibrary(this.state.files, this.emitter);
    }

    public renderProceed(): ReactNode {
        if (
            this.state.files.length === 0
            || this.state.files.every((file) => file.status === TrackReviewStatus.SAVED)
        ) {
            return (
                <div className="saving-proceed">
                    <a className="btn" href="/library/tracks">
                            <span className="btn-icon">
                            <ArrowRight/>
                            </span>
                        Go to Library
                    </a>
                </div>
            );
        }

        return (
            <div className="saving-proceed">
                <button className="btn" type="button" onClick={() => this.handleProceed()}>
                    <span className="btn-icon">
                        <Check/>
                        Add to Library
                    </span>
                </button>
            </div>
        );
    }

    private renderStatusIcon(status: TrackReviewStatus): ReactNode {
        switch (status) {
            case TrackReviewStatus.SAVED:
                return (
                    <Check color="mediumseagreen"/>
                );

            case TrackReviewStatus.ERROR:
                return (
                    <X color="orangered"/>
                );

            case TrackReviewStatus.PENDING:
                return (
                    <Clock color="darkgray"/>
                );

            case TrackReviewStatus.SAVING:
                return (
                    <UploadCloud color="cornflowerblue"/>
                );
        }
    }
}

render(<TaggingView files={__trackFiles as TrackToReview[]}/>, document.querySelector('#react-content'));
