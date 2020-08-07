import './css/review.scss';
import React, { ReactNode } from 'react';
import { render } from 'react-dom';
import { Emitter } from 'event-kit';
import $ from 'jquery';
import { generateUrl, Route } from './common/Routing';
import Icon, { Icons } from './common/Icons';

enum TrackReviewStatus {
    PENDING = 'pending',
    SAVING = 'saving',
    SAVED = 'saved',
    ERROR = 'error',
}

type TrackToReview = {
    readonly id: string;
    readonly filename: string;
    title: string | null;
    artists: string[] | null;
    albumArtists: string[] | null;
    album: string | null;
    trackNo: number | null;
    status: TrackReviewStatus;
};

declare var __tracksToReview: TrackToReview[];

function addFilesToLibrary(files: TrackToReview[], emitter: ReviewEmitter) {
    const file = files.find((file) => file.status === TrackReviewStatus.PENDING);
    if (!file) {
        return;
    }

    file.status = TrackReviewStatus.SAVING;
    emitter.emit('progress', { file });

    $.ajax({
        url: generateUrl(Route.AJAX_TRACKS_ADD_TO_LIBRARY),
        type: 'post',
        dataType: 'json',
        data: { id: file.id },
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

type ReviewEmissions = {
    progress: { file: TrackToReview };
};

type ReviewEmitter = Emitter<ReviewEmissions, ReviewEmissions>;

type ReviewFormProps = {
    files: TrackToReview[];
};

type ReviewFormState = {
    files: TrackToReview[];
};

class ReviewForm extends React.Component<ReviewFormProps, ReviewFormState> {
    private readonly emitter: ReviewEmitter;

    public constructor(props: ReviewFormProps) {
        super(props);

        this.state = { files: props.files };
        this.emitter = new Emitter<ReviewEmissions, ReviewEmissions>();
        this.emitter.on('progress', ({ file }) => {
            this.forceUpdate();
        });
    }

    public render(): ReactNode {
        return (
            <div className="review">
                <div className="review-items">
                    {this.state.files.map((file) => (
                        <div className="review-item" key={file.id}>
                            <div className="review-item-status">
                                {this.renderStatusIcon(file.status)}
                            </div>
                            <div className="review-item-info">
                                <div className="review-item-name">
                                    {file.filename}
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
                <div className="review-proceed">
                    <a className="button" href={generateUrl(Route.LIBRARY_TRACKS)}>
                        <span className="button-icon">
                            <Icon icon={Icons.NEXT}/>
                        </span>
                        Go to Library
                    </a>
                </div>
            );
        }

        return (
            <div className="review-proceed">
                <button className="button" type="button" onClick={() => this.handleProceed()}>
                    <span className="button-icon">
                        <Icon icon={Icons.FINISH}/>
                        Add to Library
                    </span>
                </button>
            </div>
        );
    }

    private renderStatusIcon(status: TrackReviewStatus): ReactNode {
        switch (status) {
            case TrackReviewStatus.PENDING:
                return <Icon icon={Icons.PENDING}/>;

            case TrackReviewStatus.SAVING:
                return <Icon icon={Icons.SAVING}/>;

            case TrackReviewStatus.SAVED:
                return <Icon icon={Icons.SUCCESS}/>;

            case TrackReviewStatus.ERROR:
                return <Icon icon={Icons.ERROR}/>;
        }
    }
}

render(<ReviewForm files={__tracksToReview as TrackToReview[]}/>, document.querySelector('#react-content'));
