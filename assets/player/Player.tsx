import React, { useEffect, useState } from 'react';
import { Empty, Loaded, PlaybackController, PlaybackEmitter, PlaybackStatus } from './PlaybackTypes';
import { Track } from '../tracks/TrackTypes';
import Duration from '../common/Duration';

import '../css/player.scss';
import Icon, { Icons } from '../common/Icons';
import useDisposableEffect from '../common/useDisposableEffect';

type PlayerProps = {
    controller: PlaybackController;
};

type PlayerState = {
    tracks: Track[];
    currentTrack: Track | null;
};

const Player: React.FC<PlayerProps> = ({ controller }) => {
    const emitter = controller.getEmitter();
    const [status, setStatus] = useState<PlaybackStatus>(new Empty());
    const [state, setState] = useState<PlayerState>({
        tracks: [],
        currentTrack: null,
    });

    function handleStatus(status: PlaybackStatus) {
        setStatus(status);
    }

    function handleTrackChange(track: Track | null) {
        setState({ ...state, currentTrack: track });
    }

    useDisposableEffect(() => {
        return emitter.on('status', handleStatus);
    }, [handleStatus]);

    useDisposableEffect(() => {
        return emitter.on('trackChange', handleTrackChange);
    }, [handleStatus]);

    useEffect(() => {
        document.body.classList.add('player-visible');

        return () => {
            document.body.classList.remove('player-visible');
        };
    });

    function handlePlayPause() {
        if (status instanceof Empty) {
            return;
        }

        if (status.paused) {
            controller.resume();
        } else {
            controller.pause();
        }
    }

    const placeholderDuration = Duration.fromSeconds(0);
    const position = status instanceof Loaded
        ? status.position
        : placeholderDuration;
    const totalDuration = status instanceof Loaded
        ? status.totalDuration
        : placeholderDuration;

    const sliderWidth = (
        status instanceof Loaded
            ? status.position.percentageOf(status.totalDuration)
            : 0
    ) + '%';

    const isPlayingOrPaused = state.currentTrack !== null;
    const hasPrevious = isPlayingOrPaused;
    const hasNext = isPlayingOrPaused;
    const showPauseAction = status instanceof Loaded && !status.paused;
    const playPauseIcon = showPauseAction ? Icons.PAUSE : Icons.PLAY;

    function handleSliderClick(e: React.MouseEvent<HTMLDivElement>): void {
        if (!isPlayingOrPaused) {
            return;
        }

        // We have to use e.target.
        const slider = e.target as HTMLDivElement;
        const relativePosition = (e.pageX - slider.offsetLeft) / slider.offsetWidth;
        const position = totalDuration.fraction(relativePosition);

        controller.seek(position);
    }

    function handleSliderMouseMove(e: React.MouseEvent<HTMLDivElement>): void {
        if (isPlayingOrPaused) {
            return;
        }

        //
    }

    return (
        <div className="player" id="persistent-root" data-turbolinks-permanent={true}>
            <div className="player-wrap">
                <div className="player-controls">
                    <button type="button" className="player-button player-button-previous"
                            disabled={!hasPrevious}>
                        <Icon icon={Icons.TRACK_PREVIOUS}/>
                    </button>
                    <button type="button" disabled={!isPlayingOrPaused} onClick={handlePlayPause}
                            className={`focus-outline player-button player-button-play-pause ${(showPauseAction ? 'player-button-pause' : 'player-button-play')}`}>
                        <Icon icon={playPauseIcon}/>
                        <span className="sr-only">{showPauseAction ? 'Pause' : 'Play'}</span>
                    </button>
                    <button type="button" className="player-button player-button-previous"
                            disabled={!hasNext}>
                        <Icon icon={Icons.TRACK_NEXT}/>
                    </button>
                </div>
                <div className="player-position-info">
                    <div className="player-position">
                        {position.toString()}
                    </div>
                    <div className="player-position-slider"
                         onClick={handleSliderClick}
                         onMouseMove={handleSliderMouseMove}
                    >
                        <div className="player-position-slider-track" style={{ width: sliderWidth }}/>
                    </div>
                    <div className="player-total-duration">
                        {totalDuration.toString()}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Player;
