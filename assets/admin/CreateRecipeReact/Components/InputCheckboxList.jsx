import React from "react";
import InputCheckbox from "./InputCheckbox";

export default function ({state = {}, handleChange}) {

    return Object.keys(state).map((v, k) => {
        return (
            <InputCheckbox handleChange={handleChange} key={v} label={v} checked={state[v]} />
        )
    })
}