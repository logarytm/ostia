import React, { useState } from 'react';
import { Empty, Loaded, PlaybackController, PlaybackEmitter, PlaybackStatus } from './PlaybackTypes';
import { Track } from '../tracks/TrackTypes';
import { Pause, Play, SkipForward, SkipBack } from 'react-feather';
import '../css/player.css';
import Duration from '../common/Duration';

type PlayerProps = {
    emitter: PlaybackEmitter;
    controller: PlaybackController;
    tracks: Track[];
    currentTrack: Track | null;
};

const PlayerControls: React.FC<PlayerProps> = ({ currentTrack, controller, emitter }) => {
    const [status, setStatus] = useState<PlaybackStatus>(new Empty());

    emitter.on('status', (newStatus) => setStatus(newStatus));

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

    const isPlayingOrPaused = currentTrack !== null;
    const isPlaying = status instanceof Loaded && status.paused;
    const hasPrevious = isPlayingOrPaused;
    const hasNext = isPlayingOrPaused;

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

    return (
        <div className="player">
            <div className="player-wrap">
                <div className="player-controls">
                    <button type="button" className="player-button player-button-previous"
                            disabled={!hasPrevious}>
                        <SkipBack/>
                    </button>
                    <button type="button" className="player-button player-button-play-pause"
                            disabled={!isPlayingOrPaused}
                            onClick={handlePlayPause}>
                        {(status instanceof Loaded && !status.paused)
                            ? <Pause/>
                            : <Play/>
                        }
                        <span className="sr-only">{isPlaying ? 'Pause' : 'Play'}</span>
                    </button>
                    <button type="button" className="player-button player-button-previous"
                            disabled={!hasNext}>
                        <SkipForward/>
                    </button>
                </div>
                <div className="player-position-info">
                    <div className="player-position">
                        {position.toString()}
                    </div>
                    <div className="player-position-slider" onClick={handleSliderClick}>
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

export default PlayerControls;
