import {useCallback, useState} from "react";
import {inputSetError} from "../Utils/functions";

export default function (errorRef) {

    const [state, setState] = useState([{
        id: 1,
        value: '',
        error: '',
        nameId: 'stage'
    }])

    const handleAdd = useCallback((e) => {
        e.preventDefault()
        setState(prevState => {
            const newState = [...prevState]
            const newObj = {
                id: newState[newState.length - 1].id + 1,
                error: '',
                value: '',
                nameId: 'stage'
            }
            newState.push(newObj)
            return newState
        })
    }, [])

    const handleChange = useCallback((e) => {
        setState(prevState => {
            const newState = [...prevState]
            newState.forEach(s => {
                if (s.id == e.target.getAttribute('name')) {
                    s.value = e.target.value
                }
            })

            return newState
        })
    }, [])

    const handleBlur = useCallback((e) => {
        inputSetError(setState, e.target.value.length < 5, errorRef, "L'étape doit contenir au moin 5 lettres.", e.target.name, true)
    }, [])

    return {
        handleChange,
        state,
        handleBlur,
        handleAdd,
        setState
    }
}