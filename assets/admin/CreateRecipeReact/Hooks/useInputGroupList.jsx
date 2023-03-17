import {useCallback, useMemo, useState} from "react";

export default function useInputGroupList (inputNames = []) {

    const initialValue = [
        {
            'id' : 1,
        }
    ]

    inputNames.forEach((v) => {
        initialValue[0][v] = ''
    })

    const [inputs, setInputs] = useState(initialValue)

    const handleChange = useCallback((e) => {
        const index = e.target.getAttribute('data-index')
        const type = e.target.getAttribute('data-inputname')

        setInputs(prevState => {
            const newState = [...prevState]
            newState[index][type] = e.target.value
            return newState
        })
    }, [])

    const handleRemove = useCallback((e) => {
        e.preventDefault()
        const index = e.target.getAttribute('data-index')

        setInputs(prevState => {
            const newState = [...prevState]
            newState.splice(index, 1)
            return newState
        })
    }, [])

    const handleAdd = useCallback((e) => {
        e.preventDefault()
        setInputs(prevState => {

            const newState = [...prevState]
            const newId = newState[newState.length - 1].id + 1
            const newValue = {
                id : newId
            }
            inputNames.forEach(v => {
                newValue[v] = ''
            })
            newState.push(newValue)
            return newState
        })

    }, [])


    return useMemo(() => ({
        inputs,
        handleAdd,
        handleChange,
        handleRemove,
        setInputs
    }), [inputs, handleAdd, handleChange, handleRemove, setInputs])
}