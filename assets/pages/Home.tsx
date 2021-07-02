import React from "react";
import TrackList from "../trackList";
import { PlaybackController } from '../player/PlaybackTypes';

export default function Home({ controller }: { controller: PlaybackController }) {
    return (
        <>
            <TrackList controller={controller}/>
        </>
    )
};
