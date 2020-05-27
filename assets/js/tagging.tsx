import '../css/tagging.css';
import React, { ReactNode } from 'react';
import { render } from 'react-dom';
import { Check } from 'react-feather';

type TaggingTrackFile = {
    readonly name: string;
    title: string | null;
    artists: string[] | null;
    albumArtists: string[] | null;
    album: string | null;
    trackNo: number | null;
};

declare var __trackFiles: TaggingTrackFile[];

type TaggingViewProps = {
    files: TaggingTrackFile[];
};

type TaggingViewState = {
    files: TaggingTrackFile[];
};

class TaggingView extends React.Component<TaggingViewProps, TaggingViewState> {
    public constructor(props: TaggingViewProps) {
        super(props);

        this.state = { files: props.files };
    }

    public render(): ReactNode {
        return (
            <div className="tagging-view">
                <div className="help">
                    Tagging is not available yet. Please click “Add to Library”.
                </div>
                {this.renderProceed()}
            </div>
        );
    }

    public renderProceed(): ReactNode {
        return (
            <div className="tagging-view-proceed">
                <button className="btn" type="button">
                    <span className="btn-icon">
                        <Check/>
                        Add to Library
                    </span>
                </button>
            </div>
        );
    }
}

render(<TaggingView files={__trackFiles as TaggingTrackFile[]}/>, document.querySelector('#react-content'));
