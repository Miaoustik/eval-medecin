import {useCallback, useMemo, useState} from "react";

export default function useInputGroupList (inputNames = [], firstRequired = true) {

    let initialValue = [
        {
            'id' : 1,
        }
    ]

    inputNames.forEach((v) => {
        initialValue[0][v] = ''
    })

    const [first, setFirst] = useState(firstRequired)


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
        if (first === false && firstRequired === false) {
            setFirst(true)
        } else {
            setInputs(prevState => {

                const newState = [...prevState]
                const newId = newState[newState.length - 1].id + 1
                const newValue = {
                    id: newId
                }
                inputNames.forEach(v => {
                    newValue[v] = ''
                })
                newState.push(newValue)
                return newState
            })
        }
    }, [inputNames, setInputs, first, firstRequired])


    return useMemo(() => ({
        inputs,
        handleAdd,
        handleChange,
        handleRemove,
        setInputs,
        inputNames,
        firstRequired,
        first
    }), [inputs, handleAdd, handleChange, handleRemove, setInputs, inputNames, firstRequired, first])
}