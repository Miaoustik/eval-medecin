import {useCallback, useMemo, useState} from "react";
import {inputSetError} from "../Utils/functions";
import useInput from "./useInput";

export default function (errorRef) {

    const {state, setState, handleChange} = useInput({
        value: '',
        error: ''
    })

    const handleBlur = useCallback((e) => {
        inputSetError(setState, e.target.value.length < 3, errorRef, 'Le titre doit avoir 3 lettres au minimum')
    }, [errorRef, setState])


    return useMemo(() => ({
        value: state.value,
        error: state.error,
        handleChange,
        handleBlur,
    }), [
        state.value,
        state.error,
        handleChange,
        handleBlur
    ])
}