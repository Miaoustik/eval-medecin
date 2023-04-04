import {useCallback, useMemo} from "react";
import {inputSetError} from "../Utils/functions";
import useInput from "./useInput";

export default function (errorRef) {

    const {state, setState, handleChange} = useInput({
        value: '',
        error: ''
    })

    const handleBlur = useCallback((e) => {
        inputSetError(setState, e.target.value.length < 5, errorRef, 'La description doit avoir 5 lettres au minimum')
    }, [errorRef, setState])


    return useMemo(() => ({
        value: state.value,
        error: state.error,
        handleChange,
        handleBlur,
        setState
    }), [
        state.value,
        state.error,
        handleChange,
        handleBlur
    ])
}